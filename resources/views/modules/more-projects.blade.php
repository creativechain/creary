<div class="row">
    <div class="col-md-12"><p class="subtitle-content-publish">{{ __('lang.PUBLICATION.MORE_PROJECTS') }}</p></div>
</div>

<div class="row">
    <div class="col">
        <div class="slider slider--columns" data-autoplay="false">
            <ul class="slides">
                <li v-for="o in otherProjects" class="col-6 col-sm-6 col-md-4 mb-2">
                    <a v-bind:href="o.url" v-on:click="loadPost(o, $event)">
                        <div class="img-more-projects"
                             v-bind:style="{ 'background-image': 'url(' + getFeaturedImage(o).url + ')' }">

                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!--end of col-->
</div>
