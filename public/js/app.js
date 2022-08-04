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

// ============== GESTION COMMENTAIRE =======================
let postId = document.querySelector('.formCom').getAttribute('data-id');
let content = document.querySelector('#comment_content');
let containerCom = document.querySelector('.comment');
let nbComm = document.querySelector('#js-count-comm');

// Event Listener
document.querySelector('.buttonSend').addEventListener('click', sendComm)

// Fonction SendComm utilisé pour l'event
function sendComm() {
    fetch('/post/details/comment/' + postId, {
        method: "POST",
        headers: { 'content-type': 'Application/json' },
        body: JSON.stringify({
            'content': content.value,
        })
    })
        .then(function (res) {
            return res.json();
        }).then(function (data) {
            nbComm.textContent = data.numberOfComm;

            let comment = document.createElement("p");
            comment.classList.add("js-comment");

            if (data.userRole.includes('ROLE_EDITOR',) || data.userRole.includes('ROLE_ADMIN') || data.currentUserUsername === data.user) {
                comment.innerHTML = "<div class='fw-bolder'>" + data.user + "<small class='ms-3'>" + data.createAt + "</small></div> <div>" + data.content + "</div>  <button class='btn btn-danger rounded-3 js-remove-comment' comment-id='{{ comment.id }}'>Delete</button>"
            } else {
                comment.innerHTML = "<div class='fw-bolder'>" + data.user + "<small class='ms-3'>" + data.createAt + "</small></div> <div>" + data.content + "</div>"
            }

            containerCom.prepend(comment);

            content.value = "";

            console.log(data)
        }).catch(function (error) {
            console.log(error);
        })
}
// ===== SUPPRIMER COMMENTAIRE
// Event Listener
// document.querySelector('.js-remove-comment').addEventListener('click', removeComm)

// // Fonction SendComm utilisé pour l'event
// function removeComm() {
// }