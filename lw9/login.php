<div class="login-page">
    <div class="login-page__left-section">
        <h1 class="login-page__title">Войти</h1>
        <img src="smile-boy.jpg" alt="Улыбающийся парень" class="login-page__image">
    </div>

<form class="login-form login-page__form" id="login-form" novalidate onsubmit="return false;">
    <div class="login-form__field">
        <label class="login-form__label" for="email">Электропочта</label>
        <div class="login-form__input-wrapper" id="email-wrapper">
            <input class="login-form__input" type="email" id="email" name="email" required>
        </div>
        <span class="login-form__error" id="email-error" style="display: none;">Неверный email</span>
        <span class="login-form__description" id="email-desc">Введите электропочту в формате *****@***.**</span>
    </div>

    <div class="login-form__field">
        <label class="login-form__label" for="password">Пароль</label>
        <div class="login-form__input-wrapper" id="password-wrapper">
            <input class="login-form__input" type="password" id="password" name="password" required>
        </div>
        <span class="login-form__error" id="password-error" style="display: none;">Неверный пароль</span>
    </div>

    <button class="login-form__button" type="submit">Продолжить</button>
</form>

<script>
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailWrapper = document.getElementById('email-wrapper');
        const passwordWrapper = document.getElementById('password-wrapper');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');
        const emailDesc = document.getElementById('email-desc');

        // 1. Сбрасываем старые ошибки перед новой проверкой
        emailWrapper.classList.remove('error-border');
        passwordWrapper.classList.remove('error-border');
        emailError.style.display = 'none';
        passwordError.style.display = 'none';
        if (emailDesc) emailDesc.style.display = 'block';

        let isValid = true;

        // 2. ПРОСТАЯ СТУДЕНЧЕСКАЯ ПРОВЕРКА НА @
        if (!email.value.includes('@')) {
            // Вариант А: Простая красная надпись снизу (раскоментируй нужный)
            emailWrapper.classList.add('error-border');
            emailError.textContent = 'В почте должна быть @'; // Меняем текст ошибки
            emailError.style.display = 'block';
            if (emailDesc) emailDesc.style.display = 'none';
            
            // Вариант Б: Совсем топорное всплывающее окно
            alert('Адрес почты должен содержать символ @'); 
            
            return; // Останавливаем скрипт
        }

        // 3. Проверка на правильность данных (твои тесты)
        if (email.value !== "test@test.com") {
            emailWrapper.classList.add('error-border');
            emailError.textContent = 'Неверный email'; // Возвращаем текст
            emailError.style.display = 'block';         
            if (emailDesc) emailDesc.style.display = 'none'; 
            isValid = false;
        }
        
        if (password.value !== "123") {
            passwordWrapper.classList.add('error-border');
            passwordError.textContent = 'Неверный пароль';
            passwordError.style.display = 'block';         
            isValid = false;
        }

        if (isValid) {
            alert('Успешный вход!');
            // Дальше можно перенаправлять пользователя
        }
    });
</script>