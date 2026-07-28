const video = document.querySelector('#demo-video');
const chapterButtons = [...document.querySelectorAll('[data-seek]')];

function selectChapter(currentTime) {
    let activeButton = chapterButtons[0];

    chapterButtons.forEach((button) => {
        const seekTime = Number(button.dataset.seek);

        if (Number.isFinite(seekTime) && seekTime <= currentTime) {
            activeButton = button;
        }
    });

    chapterButtons.forEach((button) => {
        if (button === activeButton) {
            button.setAttribute('aria-current', 'true');
        } else {
            button.removeAttribute('aria-current');
        }
    });
}

chapterButtons.forEach((button) => {
    button.addEventListener('click', () => {
        if (!(video instanceof HTMLVideoElement)) {
            return;
        }

        const seekTime = Number(button.dataset.seek);
        video.currentTime = Number.isFinite(seekTime) ? seekTime : 0;
        video.play().catch(() => {
            video.focus();
        });
    });
});

if (video instanceof HTMLVideoElement) {
    selectChapter(0);
    video.addEventListener('timeupdate', () => selectChapter(video.currentTime));
}
