<article class="post">
    <header class="post__header">
        <div class="post__user">
            <img src="<?= $post['author_img'] ?>" alt="Аватар" class="post__avatar">
            <div class="post__user-info">
                <span class="post__user-name"><?= $post['author'] ?></span>
            </div>
        </div>
        <button class="post__edit-button post__edit-btn"
                data-id="<?= $post['id'] ?>" 
                data-text="<?= htmlspecialchars($post['subtitle']) ?>"
                data-images="<?= htmlspecialchars($post['img_url']) ?>">
            <img src="red.png" alt="Редактировать" class="post__edit-icon">
        </button>
    </header>

    <div class="post__content">
        <div class="post__image-container" style="position: relative; width: 100%; display: inline-block;">
            
            <div class="post__image-counter" style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); color: #fff; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-family: sans-serif; z-index: 15;"><?= !empty($post['img_url']) ? '1/3' : '1/1' ?></div>
            
            <button class="post__slider-btn post__slider-btn--prev" style="position: absolute; top: 50%; transform: translateY(-50%); left: 12px; background: rgba(0, 0, 0, 0.4); border: none; width: 32px; height: 32px; border-radius: 50%; color: #ffffff; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 20; padding: 0; line-height: 1; outline: none; transition: background 0.2s;"
                    onmouseover="this.style.background='rgba(0, 0, 0, 0.7)'"
                    onmouseout="this.style.background='rgba(0, 0, 0, 0.4)'">&lsaquo;</button>
            
            <button class="post__slider-btn post__slider-btn--next" style="position: absolute; top: 50%; transform: translateY(-50%); right: 12px; background: rgba(0, 0, 0, 0.4); border: none; width: 32px; height: 32px; border-radius: 50%; color: #ffffff; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 20; padding: 0; line-height: 1; outline: none; transition: background 0.2s;"
                    onmouseover="this.style.background='rgba(0, 0, 0, 0.7)'"
                    onmouseout="this.style.background='rgba(0, 0, 0, 0.4)'">&rsaquo;</button>
            
            <img src="<?= $post['img_url'] ?>" 
                 alt="<?= $post['title'] ?>" 
                 class="post__image"
                 style="width: 100%; display: block; border-radius: 8px; cursor: pointer;"
                 data-images="<?= $post['img_url'] ?>,images/2.jpg,images/3.jpg">
        </div>
        
        <footer class="post__footer">
            <div class="post__likes">
                <img src="like.png" alt="" class="post__like-icon">
                <span class="post__like-count"><?= $post['likes'] ?></span>
            </div>
        </footer>
        
        <?php if (!empty($post['subtitle'])): ?>
            <div class="post__caption-wrapper" style="margin-top: 10px; font-family: sans-serif; font-size: 15px; line-height: 1.4;">
                
                <a href="/post?id=<?= $post['id'] ?>" class="post__text-link" style="text-decoration: none; color: #000000;">
                    <span class="post__caption-text"><?= htmlspecialchars($post['subtitle']) ?></span>
                </a>

                <button class="post__more-btn" 
                        data-full-text="Так красиво сегодня на улице! Настоящая зима)) Вспоминается Бродский: «Поздно ночью, в уснувшей долине, на самом дне, в городке, занесенном снегом по ручку двери...»"
                        style="display: inline-block; background: none; border: none; color: #8e8e93; font-size: 15px; font-family: sans-serif; cursor: pointer; padding: 0; margin-left: 6px; outline: none;">ещё</button>
            </div>
        <?php endif; ?>
        
        <div class="post__date" style="margin-top: 10px;"><?= $post['date'] ?></div>
    </div>
</article>