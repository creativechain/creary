<template>
    <div class="row row-list-user">
        <div class="col-md-9">
            <div class="row-list-user-display">
                <div class="user-avatar">
                    <div class="img-user-avatar"></div>
                </div>
                <div class="list-data-user">
                    <p>Comando C <span>4 days ago</span></p>
                    <p>
                        <img src="../img/icons/comments.svg" alt="" class="icon-notification-list" />
                        <span>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row-img-notification">
                <div class="img-user-notification" style="background-image: url('/img/crea-web/publish/demo-vertical.jpg'); "></div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: {
            data: Object
        }
    }
</script>
