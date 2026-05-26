<?php
// ==========================================================================
// БЛОК ОБРАБОТКИ AJAX-ЗАПРОСА (СОЗДАНИЕ И РЕДАКТИРОВАНИЕ)
// ==========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    ob_clean();
    header('Content-Type: application/json');

    $text = trim($_POST['text']);
    $postId = isset($_POST['id']) ? intval($_POST['id']) : null; // Проверяем, передан ли ID
    $uploadedImages = [];

    // Сохраняем новые файлы картинок (если их прикрепили при редактировании/создании)
    if (isset($_FILES['images'])) {
        if (!is_dir('images')) {
            mkdir('images', 0777, true);
        }
        $files = $_FILES['images'];
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $fileName = time() . '_' . basename($files['name'][$i]);
                $targetPath = 'images/' . $fileName;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $uploadedImages[] = $targetPath;
                }
            }
        }
    }

    if ($postId) {
        // ЛОГИКА ДЛЯ ЛАБЫ (РЕДАКТИРОВАНИЕ): 
        // Здесь в будущем будет: UPDATE post SET caption = $text WHERE id = $postId
        echo json_encode([
            "status" => "success",
            "message" => "Пост успешно обновлен на сервере!",
            "mode" => "edit",
            "id" => $postId
        ]);
    } else {
        // ЛОГИКА ДЛЯ ЛАБЫ (СОЗДАНИЕ):
        echo json_encode([
            "status" => "success",
            "message" => "Пост успешно создан на сервере!",
            "mode" => "create"
        ]);
    }
    exit;
}

// ... дальше твой старый код выгрузки ленты require 'database.php' ...

// ==========================================================================
// ОБЫЧНАЯ ВЫГРУЗКА ЛЕНТЫ ИЗ БАЗЫ ДАННЫХ ДЛЯ ОТОБРАЖЕНИЯ СТРАНИЦЫ
// ==========================================================================
require 'database.php'; // Твое подключение к БД

$sql = "
SELECT 
    p.id,
    p.imageUrl AS img_url,
    p.title AS title,
    p.caption AS subtitle,
    p.likesCount AS likes,
    p.createdAt AS date,
    u.name AS author,
    u.avatar AS author_img
FROM 
    post p
JOIN 
    user u ON p.userId = u.id
ORDER BY 
    p.createdAt DESC
";

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(); 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Интерфейс</title>
    <link href="home.css" rel="stylesheet">
    <link href="create_post.css" rel="stylesheet">
    <link href="login.css" rel="stylesheet"> 
</head>
<body class="body">

    <nav class="sidebar">
        <div class="sidebar__menu">
            <div class="sidebar__item sidebar__item--active" id="nav-home" style="cursor: pointer;">
                <img src="Home.png" alt="Лента" class="sidebar__icon">
            </div>
            <div class="sidebar__item" id="nav-login" style="cursor: pointer;">
                <img src="User.png" alt="Профиль" class="sidebar__icon">
            </div>
            <div class="sidebar__item" id="nav-create" style="cursor: pointer;">
                <img src="Plus.png" alt="Создать пост" class="sidebar__icon">
            </div>
        </div>
    </nav>

    <main class="feed">
        
        <div id="page-feed" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
            <?php foreach ($posts as $post): ?>
                <?php include 'post_preview.php'; ?>
            <?php endforeach; ?>
        </div>

        <div id="page-create-post" style="display: none; width: 100%;">
            <?php include 'create_post.php'; ?>
        </div>

        <div id="page-login" style="display: none; width: 100%; position: relative; min-height: 100vh;">
            <?php include 'login.php'; ?>
        </div>

    </main>

    <div id="image-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #8e8e93; z-index: 9999; flex-direction: column; justify-content: center; align-items: center;">
        <button id="modal-close" style="position: absolute; top: 30px; right: 40px; background: none; border: none; color: #fff; font-size: 36px; cursor: pointer; outline: none; padding: 10px;">&times;</button>
        <div style="position: relative; display: flex; align-items: center; justify-content: center; width: 100%; max-width: 800px;">
            <button id="modal-prev" style="position: absolute; left: 20px; background: rgba(255,255,255,0.3); border: none; color: #fff; font-size: 24px; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; outline: none; display: flex; align-items: center; justify-content: center;">&lsaquo;</button>
            <img id="modal-img" src="" alt="Full image" style="max-width: 90vw; max-height: 80vh; object-fit: contain; border-radius: 4px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
            <button id="modal-next" style="position: absolute; right: 20px; background: rgba(255,255,255,0.3); border: none; color: #fff; font-size: 24px; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; outline: none; display: flex; align-items: center; justify-content: center;">&rsaquo;</button>
        </div>
        <div id="modal-counter" style="color: #fff; margin-top: 20px; font-size: 16px; font-family: sans-serif; font-weight: 500;"></div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // 1. ЛОГИКА ПЕРЕКЛЮЧЕНИЯ СТРАНИЦ (ТЕПЕРЬ НА 3 СТРАНИЦЫ)
    // ==========================================
    const navHome = document.getElementById('nav-home');
    const navCreate = document.getElementById('nav-create');
    const navLogin = document.getElementById('nav-login');

    const pageFeed = document.getElementById('page-feed');
    const pageCreatePost = document.getElementById('page-create-post');
    const pageLogin = document.getElementById('page-login');
    
    const sidebarItems = document.querySelectorAll('.sidebar__item');

    function changePage(activeNavItem, pageToShow, pagesToHide) {
        sidebarItems.forEach(item => item.classList.remove('sidebar__item--active'));
        if (activeNavItem) activeNavItem.classList.add('sidebar__item--active');
        
        pagesToHide.forEach(page => {
            if (page) page.style.display = 'none';
        });
        
        if (pageToShow) {
            pageToShow.style.display = (pageToShow === pageFeed) ? 'flex' : 'block';
        }
    }

    if (navHome) navHome.addEventListener('click', () => changePage(navHome, pageFeed, [pageCreatePost, pageLogin]));
    if (navCreate) navCreate.addEventListener('click', () => changePage(navCreate, pageCreatePost, [pageFeed, pageLogin]));
    if (navLogin) navLogin.addEventListener('click', () => changePage(navLogin, pageLogin, [pageFeed, pageCreatePost]));


    // ==========================================
    // 2. ЛОГИКА ДЛЯ КНОПКИ «ЕЩЁ»
    // ==========================================
    const captionWrappers = document.querySelectorAll('.post__caption-wrapper');

    captionWrappers.forEach(wrapper => {
        const textElement = wrapper.querySelector('.post__caption-text');
        const moreBtn = wrapper.querySelector('.post__more-btn');

        if (!textElement || !moreBtn) return;

        const fullText = moreBtn.getAttribute('data-full-text') || '';
        const shortText = textElement.textContent.trim();

        if (shortText === fullText) moreBtn.style.display = 'none';

        moreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (moreBtn.textContent === 'ещё') {
                textElement.textContent = fullText;
                moreBtn.textContent = 'свернуть';
            } else {
                textElement.textContent = shortText;
                moreBtn.textContent = 'ещё';
            }
        });
    });


 // ==========================================
    // 3. ЛОГИКА СОЗДАНИЯ И РЕДАКТИРОВАНИЯ ПОСТА
    // ==========================================
    const cpBox = document.querySelector('.create-post-container');
    if (cpBox) {
        let uploadedImages = [];
        let currentSlideIndex = 0;
        let editingPostId = null; // Храним ID редактируемого поста (null = создание)

        // ЭЛЕМЕНТЫ ДЛЯДИНАМИЧЕСКОЙ СМЕНЫ ТЕКСТА
        const formTitle = cpBox.querySelector('.create-post__title') || cpBox.querySelector('h2'); // Найди свой тег заголовка
        const fileInput = cpBox.querySelector('#post-file-input');
        const blankZone = cpBox.querySelector('#preview-blank');
        const sliderZone = cpBox.querySelector('#preview-slider');
        const sliderImg = cpBox.querySelector('#slider-img');
        const counter = cpBox.querySelector('#slider-counter');
        const btnPrev = cpBox.querySelector('#slider-prev');
        const btnNext = cpBox.querySelector('#slider-next');
        const btnAddBlack = cpBox.querySelector('#add-photo-btn-black');
        const btnAddBlue = cpBox.querySelector('#add-photo-btn-blue');
        const textInput = cpBox.querySelector('#post-text-input');
        const publishBtn = cpBox.querySelector('#publish-btn');

        // КЛИК НА КАРАНДАШ В ЛЕНТЕ
        document.querySelectorAll('.post__edit-btn').forEach(editBtn => {
            editBtn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // 1. Получаем данные постов из дата-атрибутов
                editingPostId = editBtn.getAttribute('data-id');
                const postText = editBtn.getAttribute('data-text');
                const postImagesStr = editBtn.getAttribute('data-images');

                // Перекидываем ID в GET-параметр URL без перезагрузки страницы (для ТЗ)
                window.history.pushState({}, '', `?id=${editingPostId}`);

                // 2. Меняем интерфейс формы под "Редактирование"
                if (formTitle) formTitle.textContent = 'Редактирование поста';
                if (publishBtn) publishBtn.textContent = 'Сохранить';

                // 3. Заполняем поля старыми данными
                if (textInput) textInput.value = postText;
                
                // Если у поста были картинки, преобразуем их в массив путей для превью
                uploadedImages = [];
                if (postImagesStr) {
                    // Костыль для отображения старых картинок в слайдере:
                    // Создаем фиктивные объекты файлов, чтобы не ломать твою функцию updateSlider
                    const urls = postImagesStr.split(',').filter(url => url.trim() !== '');
                    urls.forEach(url => {
                        uploadedImages.push({ isExisting: true, url: url.trim(), type: 'image/' });
                    });
                }
                
                currentSlideIndex = 0;
                updateSlider();
                validateForm();

                // 4. Переключаем страницу на форму (твоя функция changePage)
                changePage(navCreate, pageCreatePost, [pageFeed, pageLogin]);
            });
        });

        // Модифицируем чтение картинок (FileReader нужен только для новых файлов)
        function updateSlider() {
            if (uploadedImages.length === 0) {
                if (blankZone) blankZone.style.display = 'flex';
                if (sliderZone) sliderZone.style.display = 'none';
                return;
            }
            if (blankZone) blankZone.style.display = 'none';
            if (sliderZone) sliderZone.style.display = 'block';

            const currentFile = uploadedImages[currentSlideIndex];
            
            if (currentFile.isExisting) {
                // Если картинка уже была на сервере
                if (sliderImg) sliderImg.src = currentFile.url;
            } else {
                // Если это новое добавленное фото через инпут
                const reader = new FileReader();
                reader.onload = (e) => { if (sliderImg) sliderImg.src = e.target.result; };
                reader.readAsDataURL(currentFile);
            }

            if (counter) counter.textContent = `${currentSlideIndex + 1}/${uploadedImages.length}`;
            if (btnPrev) btnPrev.style.display = (uploadedImages.length > 1) ? 'flex' : 'none';
            if (btnNext) btnNext.style.display = (uploadedImages.length > 1) ? 'flex' : 'none';
        }

        // При клике на плюс в сайдбаре — СБРАСЫВАЕМ форму в режим "Создание"
        if (navCreate) {
            navCreate.addEventListener('click', () => {
                editingPostId = null;
                window.history.pushState({}, '', window.location.pathname); // убираем ?id= из URL
                if (formTitle) formTitle.textContent = 'Создание поста';
                if (publishBtn) publishBtn.textContent = 'Поделиться';
                
                textInput.value = '';
                uploadedImages = [];
                updateSlider();
                validateForm();
            });
        }

        // --- Остальной твой код кнопок выбора файлов и стрелок слайдера остается без изменений ---
        [btnAddBlack, btnAddBlue].forEach(btn => {
            if (btn) btn.addEventListener('click', (e) => { e.preventDefault(); fileInput.click(); });
        });

        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                const files = Array.from(e.target.files);
                if (files.length === 0) return;
                files.forEach(file => { if (file.type.startsWith('image/')) uploadedImages.push(file); });
                fileInput.value = '';
                currentSlideIndex = uploadedImages.length - 1;
                updateSlider();
                validateForm();
            });
        }

        if (btnPrev) btnPrev.addEventListener('click', (e) => {
            e.preventDefault(); if (uploadedImages.length <= 1) return;
            currentSlideIndex = (currentSlideIndex - 1 + uploadedImages.length) % uploadedImages.length;
            updateSlider();
        });

        if (btnNext) btnNext.addEventListener('click', (e) => {
            e.preventDefault(); if (uploadedImages.length <= 1) return;
            currentSlideIndex = (currentSlideIndex + 1) % uploadedImages.length;
            updateSlider();
        });

        function validateForm() {
            if (!textInput || !publishBtn) return;
            publishBtn.disabled = !(uploadedImages.length > 0 && textInput.value.trim().length > 0);
        }
        if (textInput) textInput.addEventListener('input', validateForm);


        // ОТПРАВКА НА СЕРВЕР (КНОПКА "ПОДЕЛИТЬСЯ" / "СОХРАНИТЬ")
        if (publishBtn) {
            publishBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const formContainer = document.getElementById('create-post-form-container');
                const successMsg = document.getElementById('post-success-msg');
                const errorMsg = document.getElementById('post-error-msg');
                
                if (errorMsg) errorMsg.style.display = 'none';
                
                const originalBtnText = publishBtn.textContent;
                publishBtn.disabled = true;
                publishBtn.textContent = 'Сохранение...';

                const formData = new FormData();
                formData.append('text', textInput.value.trim());
                
                // Если мы в режиме редактирования, докидываем ID в запрос
                if (editingPostId) {
                    formData.append('id', editingPostId);
                }

                uploadedImages.forEach(file => {
                    if (!file.isExisting) { // Отправляем на сервер только НАСТОЯЩИЕ новые файлы
                        formData.append('images[]', file); 
                    }
                });

                try {
                    const response = await fetch('home.php', { 
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        // Меняем текст сообщения об успехе в зависимости от режима
                        if (successMsg) {
                            successMsg.querySelector('p').textContent = editingPostId 
                                ? 'Пост успешно обновлен!' 
                                : 'Пост успешно сохранен!';
                        }

                        if (formContainer) formContainer.style.display = 'none';
                        if (successMsg) successMsg.style.display = 'block';
                        
                        // СБРОС И ОЧИСТКА
                        textInput.value = '';
                        uploadedImages = [];
                        editingPostId = null;
                        window.history.pushState({}, '', window.location.pathname); // Чистим URL
                        updateSlider();
                        validateForm();
                    } else {
                        throw new Error('Ошибка сервера');
                    }

                } catch (error) {
                    if (errorMsg) {
                        errorMsg.textContent = 'Ошибка при сохранении изменений. Попробуйте еще раз.';
                        errorMsg.style.display = 'block';
                    }
                } finally {
                    publishBtn.disabled = false;
                    publishBtn.textContent = originalBtnText;
                }
            });
        }

        // Логика кнопки "Написать еще" / "Вернуться"
        const writeAnotherBtn = document.getElementById('write-another-btn');
        if (writeAnotherBtn) {
            writeAnotherBtn.addEventListener('click', () => {
                const formContainer = document.getElementById('create-post-form-container');
                const successMsg = document.getElementById('post-success-msg');
                
                if (successMsg) successMsg.style.display = 'none';
                if (formContainer) formContainer.style.display = 'block';
                
                // Возвращаем дефолтный заголовок на всякий случай
                if (formTitle) formTitle.textContent = 'Создание поста';
                if (publishBtn) publishBtn.textContent = 'Поделиться';
            });
        }
    }


    // ==========================================
    // 5. ЛОГИКА МОДАЛЬНОГО ОКНА ДЛЯ ФОТО
    // ==========================================
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-img');
    const modalPrev = document.getElementById('modal-prev');
    const modalNext = document.getElementById('modal-next');
    const modalCloseBtn = document.getElementById('modal-close');
    const modalCounter = document.getElementById('modal-counter');

    let modalImagesArray = [];
    let modalCurrentIdx = 0;

    function handleEscPress(e) {
        if (e.key === 'Escape') closeModal();
    }

    window.openGalleryModal = function(imagesArray, startIndex) {
        if (!modal) return;
        modalImagesArray = imagesArray;
        modalCurrentIdx = startIndex;
        updateModalView();
        modal.style.display = 'flex';
        document.addEventListener('keydown', handleEscPress);
    };

    function closeModal() {
        if (!modal) return;
        modal.style.display = 'none';
        document.removeEventListener('keydown', handleEscPress);
    }

    function updateModalView() {
        modalImg.src = modalImagesArray[modalCurrentIdx];
        modalCounter.textContent = `${modalCurrentIdx + 1} из ${modalImagesArray.length}`;
        
        if (modalImagesArray.length <= 1) {
            modalPrev.style.display = 'none';
            modalNext.style.display = 'none';
            modalCounter.style.display = 'none';
        } else {
            modalPrev.style.display = 'flex';
            modalNext.style.display = 'flex';
            modalCounter.style.display = 'block';
        }
    }

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);

    if (modalPrev) {
        modalPrev.addEventListener('click', () => {
            modalCurrentIdx = (modalCurrentIdx - 1 + modalImagesArray.length) % modalImagesArray.length;
            updateModalView();
        });
    }

    if (modalNext) {
        modalNext.addEventListener('click', () => {
            modalCurrentIdx = (modalCurrentIdx + 1) % modalImagesArray.length;
            updateModalView();
        });
    }


    // ==========================================
    // 4. ОЖИВЛЯЕМ СЛАЙДЕРЫ В ЛЕНТЕ
    // ==========================================
    const allFeedPosts = document.querySelectorAll('.post');

    allFeedPosts.forEach(post => {
        const imgElement = post.querySelector('.post__image');
        const btnPrev = post.querySelector('.post__slider-btn--prev');
        const btnNext = post.querySelector('.post__slider-btn--next');
        const counterElement = post.querySelector('.post__image-counter');

        if (!imgElement) return;

        const imagesData = imgElement.getAttribute('data-images');
        if (!imagesData) return;

        const imagesArray = imagesData.split(',').map(url => url.trim()).filter(url => url !== '');
        let activeIndex = 0;

        imgElement.addEventListener('click', () => {
            if (typeof window.openGalleryModal === 'function') {
                window.openGalleryModal(imagesArray, activeIndex);
            }
        });

        if (imagesArray.length <= 1) {
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'none';
            if (counterElement) counterElement.style.display = 'none';
            return;
        }

        function syncFeedSlider() {
            imgElement.src = imagesArray[activeIndex];
            if (counterElement) counterElement.textContent = `${activeIndex + 1}/${imagesArray.length}`;
        }

        if (btnPrev) btnPrev.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            activeIndex = (activeIndex - 1 + imagesArray.length) % imagesArray.length;
            syncFeedSlider();
        });

        if (btnNext) btnNext.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            activeIndex = (activeIndex + 1) % imagesArray.length;
            syncFeedSlider();
        });
    });

});
</script>
</body>
</html>