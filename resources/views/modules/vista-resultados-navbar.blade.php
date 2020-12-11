<div class="container container-result-search">
    <div v-show="tags.items.length > 0" class="row">
        <div class="col-md-3 col-lg-2 dropdown__content p-0">
            <ul class="menu-vertical ul-text text-left">
                <li  v-for="t in tags.items">
                    <a v-bind:href="'/popular/' + t.name" class="text-capitalize" target="_blank">
                        @{{ t.name }}
                    </a>
                </li>
            </ul>
        </div>
    </div><!--end row-->
    <div v-show="accounts.items.length > 0" class="row row-name-img">
        <div class="col-md-3 col-lg-2 dropdown__content p-0">
            <ul class="menu-vertical ul-name-img mb-0">
                <li v-for="a in accounts.items">
                    <a v-bind:href="'/@' + a.name" target="_blank">
                        <div class="row-flex">
                            <div class="user-avatar size-25-avatar">
                                <avatar v-bind:account="a"></avatar>
<!--                                <div class="img-user-avatar size-25-avatar" v-bind:style="{ backgroundImage: 'url(' + a.metadata.avatar.url + ')' }"></div>-->
                            </div>
                            <div class="media-body text-truncate text-left">
                                <p class="dropdown-autor-span-name mb-0">@{{ a.public_name || '@' + a.name }}</p>
                                <p class="dropdown-user-span-name mb-0"> @{{ '@' + a.name }}</p>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="see-all-members">
                <a href="" >See all members</a>
            </div>

        </div>

    </div><!--end row-->
</div><!--end container-->
