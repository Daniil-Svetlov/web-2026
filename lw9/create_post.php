<div class="create-post-container">

    <div id="post-success-msg" style="display: none; text-align: center; padding: 60px 20px;">
        <h2 style="color: #222222; font-size: 24px; margin-bottom: 20px;">Пост успешно сохранен!</h2>
        <button type="button" id="write-another-btn" style="padding: 12px 24px; background: #EFEFEF; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500;">Написать еще</button>
    </div>

    <div id="create-post-form-container">
        <input type="file" id="post-file-input" multiple accept="image/*" style="display: none;">

        <div id="preview-blank" class="create-post__blank">
            <div class="create-post__blank-content">
                <span style="font-size: 48px; color: #808080;">🖼️</span> 
                <button type="button" id="add-photo-btn-black" class="create-post__btn-black">Добавить фото</button>
            </div>
        </div>

        <div id="preview-slider" class="create-post__slider" style="display: none;">
            <div class="create-post__slider-wrapper">
                <img id="slider-img" src="" alt="Превью" class="create-post__slider-img">
                
                <button type="button" id="slider-prev" class="create-post__arrow create-post__arrow--left">&lt;</button>
                <button type="button" id="slider-next" class="create-post__arrow create-post__arrow--right">&gt;</button>
                
                <div id="slider-counter" class="create-post__counter"></div>
            </div>
        </div>

        <div class="create-post__action-line">
            <button type="button" id="add-photo-btn-blue" class="create-post__btn-blue">
                <span>+</span> Добавить фото
            </button>
        </div>

        <div class="create-post__input-wrapper">
            <input type="text" id="post-text-input" placeholder="Добавьте подпись..." class="create-post__text-input" autocomplete="off">
        </div>

        <div id="post-error-msg" style="display: none; color: #FF0000; font-size: 14px; text-align: center; margin-bottom: 12px;"></div>

        <div class="create-post__submit-wrapper">
            <button type="submit" id="publish-btn" class="create-post__publish-btn" disabled>Поделиться</button>
        </div>
    </div>

</div>