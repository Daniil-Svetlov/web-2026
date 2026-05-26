// Пишем прямо в начале файла alert, чтобы ЖЕЛЕЗОБЕТОННО проверить, обновился ли JS в браузере.
// Как только скрипт заработает, эту строчку (alert) можно будет удалить.
alert("Скрипт успешно загружен и работает!");

document.addEventListener('DOMContentLoaded', () => {
    
    // Находим абсолютно все контейнеры картинок на странице
    const imageContainers = document.querySelectorAll('.post__image-container');

    imageContainers.forEach(container => {
        const img = container.querySelector('.post__image');
        const counter = container.querySelector('.post__image-counter');
        const btnPrev = container.querySelector('.post__slider-btn--prev');
        const btnNext = container.querySelector('.post__slider-btn--next');

        // Проверяем, что все элементы внутри конкретного блока на месте
        if (!img || !counter || !btnPrev || !btnNext) return;

        // Достаем строчку с картинками из data-images
        const imagesRaw = img.getAttribute('data-images');
        if (!imagesRaw) return;

        const images = imagesRaw.split(',');
        let currentIndex = 0;

        // Функция обновления контента
        function changeImage(index) {
            currentIndex = index;
            img.src = images[currentIndex];
            counter.textContent = `${currentIndex + 1}/${images.length}`;
        }

        // Клик "Вперед"
        btnNext.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Не даем клику открыть модальное окно
            
            let nextIndex = currentIndex + 1;
            if (nextIndex >= images.length) {
                nextIndex = 0;
            }
            changeImage(nextIndex);
        });

        // Клик "Назад"
        btnPrev.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Не даем клику открыть модальное окно
            
            let prevIndex = currentIndex - 1;
            if (prevIndex < 0) {
                prevIndex = images.length - 1;
            }
            changeImage(prevIndex);
        });
    });
});