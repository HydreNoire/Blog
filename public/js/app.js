function onClickBtnLike(event) {
    event.preventDefault();

    const url = this.href;
    const likeCount = this.querySelector('span.js-likes');
    const iconLike = this.querySelector('span.star');

    axios.get(url).then(function (response) {
        likeCount.textContent = response.data.likes;

        let iconLikeStyle = getComputedStyle(iconLike);
        let iconLikeColor = iconLikeStyle.getPropertyValue("color");
        console.log(iconLikeColor)

        if (iconLikeColor === "rgb(128, 128, 128)") {
            iconLike.style.color = "rgb(255, 255, 0)";
            console.log('ca marche yellow')
        } else if (iconLikeColor === "rgb(255, 255, 0)") {
            iconLike.style.color = "rgb(128, 128, 128)";
            console.log('ca marche grey')
        }
    }).catch(function (e) {
        if (e.response.status === 403) {
            alert("You can't like if you're not connected");
        } else {
            alert("Errors occurs, try again later");
        }
    })
}

document.querySelectorAll('a.js-like').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike)
});
