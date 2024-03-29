<section v-cloak>
    <div class="border-post-view">
        <div class="container" style="background-color: white;border-radius: 10px 10px;">
            <div class="row row-title">
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="row-user-name">
                        <div class="user-avatar">
                            <a v-bind:href="'/@' + state.author.name">
                                <avatar v-bind:account="state.author"></avatar>
                            </a>
                        </div>
                        <div class="user-data">
                            <h3 class="mb-0 text-truncate title">@{{ state.post.title }}</h3>
                            <ul class="list-inline list-unstyled w-100 ul-buzz-profile">
                                <li class="list-inline-item float-left">
                                    <username class="name color--link" v-bind:inline="1" v-bind:user="state.author.name" v-bind:name="state.author.metadata.publicName"></username>
                                </li>
                                <li class="li-buzz list-inline-item float-left">
                                    <div class="img-buzz" v-bind:class="getBuzzClass(state.author)"></div>
                                </li>
                                <li class="list-inline-item"><span class="text-buzz">@{{ state.author.buzz.formatted }}</span></li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-6 text-right mt-3 mb-3 mb-md-0 mt-md-0">
                    <ul class="stats-post">
                        <li class="list-inline-item">
                            <btn-follow v-if="session && !isSameUser() && !state.author.buzz.blocked"
                                        v-on:follow="onFollow" v-bind:session="session"
                                        v-bind:account="user"
                                        v-bind:followuser="state.post.author" >

                            </btn-follow>
                            <div v-else-if="session && isSameUser()" class="btn btn--primary" v-on:click="editPost()">
                                    <span class="btn__text">
                                        {{ __('lang.BUTTON.EDIT_POST') }}
                                    </span>
                            </div>
                        </li>
                        <li v-if="!state.author.buzz.blocked" class="ul-social list-inline-item">
                            <div class="row-likes">
                                <div class="d-flex w-100">
                                    <div>
                                        <post-like v-on:vote="onVote" v-bind:session="session" v-bind:post="state.post"></post-like>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class="list-inline-item">
                            <div class="dropdown dropdown-price">
                                <span class="dropdown__trigger">
                                    <post-amount v-bind:value="getFriendlyPayout()"
                                                 v-bind:symbol="'$'"
                                                 v-bind:symbefore="true" >
                                    </post-amount>
                                    <i class="stack-down-open"></i>
                                </span>
                                <div class="dropdown__container price">
                                    <div class="">
                                        <div class="row">
                                            <div  v-if="state.post.refused_payouts" class="col-12 col-sm-12 col-md-12 dropdown__content amount-post-view-home">
                                                <p class="error-color-form">{{ __('lang.HOME.DROPDOWN_REFUSED_PAYOUT') }} <span class="refused-payout">@{{ getPayout() }}</span></p>
                                            </div>
                                            <div v-else class="col-12 col-sm-12 col-md-12 dropdown__content amount-post-view-home">
                                                <p class="title">@{{ hasPaid() ? lang.HOME.DROPDOWN_PAST_PAYOUT : lang.HOME.DROPDOWN_PENDING_PAYOUT }} @{{ getPayout() }}</p>
                                                <p v-if="!hasPaid()">
                                                    @{{ getPendingPayouts(state.post) }}
                                                </p>
                                                <p>@{{ getPayoutPostDate() }}</p>
                                                <p v-if="hasPromotion()">{{ __('lang.HOME.PROMOTION_COST') }}: @{{ getPromotion() }}</p>
                                            </div>
                                        </div><!--end row-->
                                    </div><!--end container-->
                                </div><!--end dropdown container-->
                            </div>
                        </li>
                        <li class="list-inline-item">
                            <div class="dropdown dropdown-price dropdown-delete">
                                <span class="dropdown__trigger">
                                    <i class="fa fa-ellipsis-v ml-3" aria-hidden="true"></i>
                                </span>
                                <div class="dropdown__container price">
                                    <div class="">
                                        <div class="row">
                                            <div v-if="session && isSameUser()" class="col-12 col-sm-12 col-md-12 dropdown__content amount-post-view-home">
                                                <a href="#modal-delete" class="delete-btn">{{ __('lang.BUTTON.DELETE_PROJECT') }}</a>
                                            </div>
                                            <div  v-else-if="state.post.refused_payouts" class="col-12 col-sm-12 col-md-12 dropdown__content amount-post-view-home">
                                                <p class="error-color-form">{{ __('lang.HOME.DROPDOWN_REFUSED_PAYOUT') }} <span class="refused-payout">@{{ getPayout() }}</span></p>
                                            </div>
                                        </div><!--end row-->
                                    </div><!--end container-->
                                </div><!--end dropdown container-->
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container"  style="background-color: white">
            <div class="row background-content-post row-content-post">
                <div class="col-md-12 img-post-view content-post" >
                    <div v-if="state.author.profile_blocked" >
                        <p class="error-color-form font-weight-bold">
                            {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_TITLE') }}
                        </p>
                        <span>
                            {{ __('lang.PUBLICATION.BLOCKED_PROFILE_ALERT_MESSAGE') }}
                        </span>

                    </div>
                    <div v-else-if="state.author.buzz.blocked" >
                        <p class="error-color-form font-weight-bold">
                            {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_TITLE') }}
                        </p>
                        <span>
                            {{ __('lang.PUBLICATION.BLOCKED_USER_ALERT_MESSAGE') }}
                        </span>

                    </div>
                    <div v-else>
                        <div v-for="el in state.post.body">
                            <div v-if="el != null">
                                <div v-if="el.type.indexOf('text/html') > -1" v-html="linkfy(el.value)" style="word-break: break-word;">

                                </div>
                                <div v-else-if="el.type.indexOf('image/') > -1" class="upload-img">
                                    <p>
                                        <img v-lazy="'https://ipfs.creary.net/ipfs/' + el.hash" v-bind:type="el.type" alt="" />
                                    </p>
                                </div>
                                <div v-else-if="el.type.indexOf('video/') > -1" class="upload-img">
                                    <p>
                                        <video controls loop>
                                            <source v-bind:src="'https://ipfs.creary.net/ipfs/' + el.hash" v-bind:type="el.type">
                                        </video>
                                    </p>
                                </div>
                                <div v-else-if="el.type.indexOf('audio/') > -1" class="upload-img">
                                    <p>
                                        <audio controls>
                                            <source v-bind:src="'https://ipfs.creary.net/ipfs/' + el.hash" v-bind:type="el.type">
                                        </audio>
                                    </p>
                                </div>
                                <div v-else-if="el.type.indexOf('embed/') > -1"
                                     class="upload-img">
                                    <div class="videodetector">
                                        <iframe class="videoembed" v-bind:src="el.value" frameborder="0" allow="fullscreen" allowfullscreen></iframe>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container row-promoter pt-3 pb-3" style="background-color: white">
            <div class="row justify-content-between">
                <div v-if="!state.author.buzz.blocked" class="col-6 col-sm-6 col-md-6">
                    <recommend-post v-bind:post="state.post" v-bind:session="session" v-bind:user="user"></recommend-post>
                </div>
                <div class="col-6 col-sm-6 col-md-6 text-right">
                    <div v-if="session && !state.author.buzz.blocked" class="row-promote justify-content-center">

                        <div>
                            <a href="#modal-promote" data-modal-id="modal-promote" class="btn btn--transparent modal-trigger">
                                <span class="btn__text color--dark">
                                    {{ __('lang.BUTTON.PROMOTE') }}
                                </span>
                            </a>


                            {{--<div v-pre>

                            </div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="navigation">
            <div v-pre>
                <div id="more-projects-navigation" v-if="!state.author.buzz.blocked && otherProjects.length > 0" class="container row-project">
                    @include('modules.more-projects')
                </div>
            </div>
        </div>
        <div v-else >
            <div v-pre>
                <div id="more-projects" v-if="!state.author.buzz.blocked && otherProjects.length > 0" class="container row-project">
                    @include('modules.more-projects')
                </div>
            </div>
        </div>


        <div class="container row-title-comment">
            <div class="row">
                <div class="col-md-12">
                    <p class="subtitle-content-publish mb-0">{{ __('lang.PUBLICATION.COMMENTS') }}</p>
                </div>
            </div>
        </div>

        <div class="container row-comment-options">
            <div class="row">
                <div class="col-md-7">
                    <div class="boxed boxed--border box-comment mb-0 mt-3">
                        <div v-if="session && !state.author.buzz.blocked" class="row">
                            <div class="col-md-12 row-comment">

                                <div class="user-avatar">
                                    <a v-bind:href="'/@' + user.name">
                                        <avatar v-bind:account="user"></avatar>
                                    </a>

                                </div>
                                <div class="textarea">
                                    <textarea name="text" placeholder="Message" rows="4" v-model="comment" v-bind:maxlength="CONSTANTS.TEXT_MAX_SIZE.COMMENT"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 mt--1 col-post-btn-comment">
                                <div v-if="active_comment_edit !== null" class="btn btn--primary" v-on:click="addComment(state.post, false, active_comment_edit)">
                                    <span  class="btn__text">
                                        {{ __('lang.BUTTON.POST_EDIT_COMMENT') }}
                                    </span>
                                </div>
                                <div v-else class="btn btn--primary" v-on:click="addComment(state.post)">
                                    <span class="btn__text">
                                        {{ __('lang.BUTTON.POST_COMMENT') }}
                                    </span>
                                </div>
                                <div v-if="active_comment_edit !== null" class="btn" v-on:click="cleanMakeComment()">
                                    <span class="btn__text text__dark">
                                        {{ __('lang.BUTTON.CANCEL_EDIT') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="title">{{ __('lang.PUBLICATION.COMMENTS') }} (@{{ state.post.children }})</h3>
                            </div>
                        </div>

                        <div v-for="(c, i) in state.post.comments">
                            <div v-if="isCommentResponse(state.post[c], state.post)" class="row">
                                <div class="col-md-12">
                                    <div class="row-post-comments">
                                        <div class="user-avatar">
                                            <a v-bind:href="'/@' + state.post[c].author">
                                                <avatar v-bind:account="state.accounts[state.post[c].author]"></avatar>
                                            </a>
                                        </div>
                                        <div class="user-comments">
                                            <p>
                                                <username v-bind:inline="1" v-bind:user="state.post[c].author" v-bind:name="state.accounts[state.post[c].author].metadata.publicName"></username>
                                                <span>@{{ dateFromNow(state.post[c].created) }}</span>
                                            </p>
                                            <p class="comment-user" v-html="linkfy(state.post[c])"></p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul class="list-inline list-unstyled ul-row-share-comment">
                                                        <li class="list-inline-item">
                                                            <comment-like v-on:vote="onVote" v-bind:session="session" v-bind:post="state.post[c]"></comment-like>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <div class="dropdown dropdown-price">
                                                                <span class="dropdown__trigger">
                                                                    <span class="amount-price">
                                                                        @{{ getPayout(state.post[c], true, 3) }}
                                                                    </span>
                                                                    <i class="stack-down-open"></i>
                                                                </span>
                                                                <div class="dropdown__container price" style="left: -756.312px;">
                                                                    <div>
                                                                        <div class="row">
                                                                            <div  v-if="state.post[c].refused_payouts"  class="dropdown__content amount-post-view-home">
                                                                                <p class="error-color-form">{{ __('lang.HOME.DROPDOWN_REFUSED_PAYOUT') }} <span class="refused-payout">@{{ getPayout(state.post[c]) }}</span></p>
                                                                            </div>
                                                                            <div v-else class="dropdown__content amount-post-view-home">
                                                                                <p class="title">@{{ hasPaid(state.post[c]) ? lang.HOME.DROPDOWN_PAST_PAYOUT : lang.HOME.DROPDOWN_PENDING_PAYOUT }} @{{ getPayout(state.post[c]) }}</p>
                                                                                <p v-if="!hasPaid()">
                                                                                    @{{ getPendingPayouts(state.post[c]) }}
                                                                                </p>
                                                                                <p>@{{ getPayoutPostDate(state.post[c]) }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li v-if="session">
                                                            <a href="#0" v-on:click="active_response = state.post[c]">{{ __('lang.PUBLICATION.REPLY') }}</a>
                                                        </li>
                                                        <li v-if="session && state.post[c].author === user.name">
                                                            <a href="#0" v-on:click="setActiveCommentEdit(state.post[c])">{{ __('lang.PUBLICATION.EDIT') }}</a>
                                                        </li>
                                                        <li v-if="session && state.post[c].author === user.name && state.post[c].children === 0" >
                                                            <a href="#0" v-on:click="removeComment(state.post[c])">{{ __('lang.PUBLICATION.DELETE') }}</a>
                                                        </li>
                                                    </ul>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row-flag">
                                            <a href="#modal-report-comment" data-modal-id="modal-report-comment"  v-if="!state.post[c].reported" class="modal-trigger link-report" v-on:click="setActiveComment(state.post[c])">
                                                <div class="a-flag" v-bind:class="{ active: state.post[c].reported, inactive: !state.post[c].reported }"></div>
                                            </a>
                                            <a href="#0" v-else class="link-report" v-on:click="vote(0, state.post[c])">
                                                <div class="a-flag" v-bind:class="{ active: state.post[c].reported, inactive: !state.post[c].reported }"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row-reply-comment">
                                        <div v-if="mustShowCommentField(state.post[c])">
                                            <textarea cols="30" rows="3" v-model="response_comment" v-bind:maxlength="CONSTANTS.TEXT_MAX_SIZE.COMMENT"></textarea>

                                            <div class="btn btn&#45;&#45;primary mt-3 mb-3 mt-md-3 mb-md-5" v-on:click="addComment(state.post[c], true, active_response_edit)">
                                                <span class="btn__text">
                                                    {{ __('lang.BUTTON.POST_COMMENT') }}
                                                </span>
                                            </div>
                                            <div class="btn btn&#45;&#45;sm mt-0 mb-3 mt-md-3 mb-md-5" v-on:click="cleanMakeResponse()">
                                                <span class="btn__text text__dark">{{ __('lang.BUTTON.CANCEL') }}</span>
                                            </div>
                                        </div>

                                        <div v-for="cc in state.post.comments">
                                            <div class="row-post-comments" v-if="isCommentResponse(state.post[cc], state.post[c])">
                                                <div class="user-avatar">
                                                    <a v-bind:href="'/@' + state.post[cc].author">
                                                        <avatar v-bind:account="state.accounts[state.post[cc].author]"></avatar>
                                                    </a>
                                                </div>
                                                <div class="user-comments">
                                                    <div>
                                                        <p>
                                                            <username v-bind:inline="1" v-bind:user="state.post[cc].author" v-bind:name="state.accounts[state.post[cc].author].metadata.publicName"></username>
                                                            <span>@{{ dateFromNow(state.post[cc].created) }}</span>
                                                        </p>
                                                    </div>

                                                    <p class="comment-user" v-html="linkfy(state.post[cc])"></p>
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <ul class="list-inline list-unstyled ul-row-share-comment">
                                                                <li class="list-inline-item">
                                                                    <comment-like v-on:vote="onVote" v-bind:session="session" v-bind:post="state.post[cc]"></comment-like>
                                                                </li>
                                                                <li class="list-inline-item">
                                                                    <div class="dropdown dropdown-price">
                                                                        <span class="dropdown__trigger">
                                                                            <span class="amount-price">
                                                                                @{{ getPayout(state.post[cc], true, 3) }}
                                                                            </span>
                                                                            <i class="stack-down-open"></i>
                                                                        </span>
                                                                        <div class="dropdown__container price" style="left: -756.312px;">
                                                                            <div>
                                                                                <div class="row" v-bind:data="state.post.refused_payouts">
                                                                                    <div  v-if="state.post[cc].refused_payouts"  class="col-12 col-sm-12 col-md-12 dropdown__content amount-post-view-home">
                                                                                        <p class="error-color-form">{{ __('lang.HOME.DROPDOWN_REFUSED_PAYOUT') }} <span class="refused-payout">@{{ getPayout(state.post[cc]) }}</span></p>
                                                                                    </div>
                                                                                    <div v-else class="col-12 col-sm-12 col-md-12 dropdown__content amount-post-view-home">
                                                                                        <p class="title">@{{ hasPaid(state.post[cc]) ? lang.HOME.DROPDOWN_PAST_PAYOUT : lang.HOME.DROPDOWN_PENDING_PAYOUT }} @{{ getPayout(state.post[cc]) }}</p>
                                                                                        <p v-if="!hasPaid()">
                                                                                            @{{ getPendingPayouts(state.post[cc]) }}
                                                                                        </p>
                                                                                        <p>@{{ getPayoutPostDate(state.post[cc]) }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li v-if="session && state.post[cc].author === user.name">
                                                                    <a href="#0" v-on:click="active_response_edit = state.post[cc]">{{ __('lang.PUBLICATION.EDIT') }}</a>
                                                                </li>
                                                                <li v-if="session && state.post[cc].author === user.name && state.post[cc].children === 0" v-on:click="removeComment(state.post[cc])">
                                                                    <a href="#0">{{ __('lang.PUBLICATION.DELETE') }}</a>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row-flag">
                                                    {{--dos estados que acompañan a la clase a-flag o inactive o active--}}

                                                    <a href="#modal-report-comment" data-modal-id="modal-report-comment"v-if="!state.post[cc].reported" class="modal-trigger link-report" v-on:click="setActiveComment(state.post[cc])">
                                                        <div class="a-flag" v-bind:class="{ active: state.post[cc].reported, inactive: !state.post[cc].reported }"></div>
                                                    </a>
                                                    <a href="#0" v-else class="link-report" v-on:click="vote(0, state.post[cc],)">
                                                        <div class="a-flag" v-bind:class="{ active: state.post[cc].reported, inactive: !state.post[cc].reported }"></div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <hr v-if="state.post.replies.length > 10 && comments_shown < state.post.replies.length" />

                        <div v-if="state.post.replies.length > 10 && comments_shown < state.post.replies.length" class="row" v-on:click="showMoreComments()">
                            <div class="col-md-12 text-center">
                                <div class="more-comments cursor-link">{{ __('lang.PUBLICATION.MORE_COMMENTS') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="boxed boxed--border box-report">
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="list-inline list-unstyled">

                                            <li>
                                                <a href="#0" class="button-report-post" v-if="state.post.reported" v-on:click="vote(0)">
                                                    <span>(@{{ state.post.down_votes.length }}) {{ __('lang.PUBLICATION.REMOVE_REPORT') }}</span>
                                                </a>
                                                <a href="#modal-report" data-modal-id="modal-report" class="button-report-post modal-trigger" v-else-if="!isSameUser()">
                                                    <span>(@{{ state.post.down_votes.length }}) {{ __('lang.PUBLICATION.REPORT_CONTENT') }}</span>
                                                </a>
                                                <a href="#0" class="button-report-post" v-else >
                                                    <span>(@{{ state.post.down_votes.length }}) {{ __('lang.PUBLICATION.REPORTS') }}</span>
                                                </a>
                                            </li>


                                            <li v-on:click="ignoreUser">
                                                <a href="#0" class="button-block-post">
                                                    <span>{{ __('lang.PUBLICATION.BLOCK_USER') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div v-if="state.post.download.resource && !state.author.buzz.blocked" class="boxed boxed--border box-comment  mt--2 mt-3">
                        <div class="row row-download">
                            <div class="col-md-12 row-format mt-0">
                                <p class="title">{{ __('lang.PUBLICATION.FORMAT') }}</p>
                                <span class="description">@{{ state.post.download.type }}</span>
                            </div>
                            <div class="col-md-12 row-format">
                                <p class="title">{{ __('lang.PUBLICATION.SIZE') }}</p>
                                <span class="description">@{{ humanFileSize(state.post.download.size) }}</span>
                            </div>
                            <div class="col-md-12 row-format">
                                <p class="title">{{ __('lang.PUBLICATION.PRICE') }}</p>
                                <span v-if="state.post.download.price.amount === 0" class="description">{{ __('lang.PUBLICATION.FREE_DOWNLOAD') }}</span>
                                <div v-else>
                                    <amount v-bind:value="state.post.download.price"></amount>
                                </div>

                            </div>
                            <div class="borderHr"></div>
                            <div class="col-md-12">
                                <ul class="ul-downloads">
                                    <div v-if="state.post.download.resource">
                                        <li v-if="state.post.download.resource">
                                            <img src="/img/icons/downloads.svg" alt="" />
                                            <p>@{{ state.post.download.times_downloaded }} {{ __('lang.PUBLICATION.DOWNLOADS') }}</p>
                                        </li>
                                    </div>
                                </ul>
                            </div>

                            <div class="borderHr"></div>


                            <div class="col-md-12 text-center mt-3">
                                <div>
                                    <a v-bind:href="session ? '#modal-download' : '#modal-login'" v-bind:data-modal-id="session ? 'modal-download' : 'modal-login'"
                                       class="btn btn--primary modal-trigger">
                                            <span class="btn__text">
                                                {{ __('lang.BUTTON.DOWNLOAD') }}
                                            </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="getNFTLink()" class="boxed boxed--border box-comment  mt--2">
                        <div class="row row-publish-tags">
                            <div class="col-md-12">
                                <p class="title mb-0">{{ __('lang.PUBLICATION.DIGITAL_COLLECTABLE') }}</p>
                            </div>
                        </div>
                        <div class="title-line">
                            <div class="borderHr"></div>
                        </div>
                        <div class="row row-publish-tags">
                            <div class="col-md-12 text-center mt-3">
                                <a v-bind:href="getNFTLink()" class="btn btn--sm btn--secondary color-secondary font-weight-bold btn-nft" target="_blank">{{ __('lang.PUBLICATION.GET_NFT') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="boxed boxed--border box-comment  mt--2">
                        <div class="row row-publish-tags">
                            <div class="col-md-12">
                                <p class="title mb-0">TAGS</p>
                                <div v-html="getLinkedTags(true)"></div>
                            </div>
                        </div>
                    </div>

                    <div class="boxed boxed--border box-comment  mt--2">
                        <div class="row row-publish-description">
                            <div class="col-md-12">
                                <p class="title mb-1">@{{ state.post.title }}</p>
                                <span class="description">@{{ state.post.metadata.description }}</span>
                                <p class="date-publish description mt-4">@{{ formatDate(state.post.created) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="boxed boxed--border box-comment  mt--2">
                        <div class="row row-publish-description">
                            <div class="col-md-12">
                                <p class="title mb-1">{{ __('lang.PUBLICATION.BENEFICIARIES') }}</p>
                                <span v-for="(b, index) in state.post.contributors" class="description">
                                    @{{ b.account }} (@{{ b.weight }}%)@{{ index+1 < state.post.contributors.length ? ',':'' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="boxed boxed--border box-blockchain-certificate pt-3">
                        <img class="certificat-flag" src="/img/crea-web/publish/flag.png" alt="" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="feature feature-2">
                                    <div class="feature__body">
                                        <h2 class="title-certificate">{{ __('lang.PUBLICATION.CERTIFICATE') }}</h2>
                                        <hr>
                                        <p>Blockchain: <a v-bind:href="'https://creascan.net' + state.post.url + '/data'" target="_blank">Project Data</a> </p>
                                        <p>Timestamp: @{{ new Date(state.post.created + "Z").toLocaleString() }}</p>
                                        <p>{{ __('lang.PUBLICATION.LICENSE') }}: <a v-bind:href="getLicense().getLink()" target="_blank">@{{ getLicense().getTags() }}</a></p>
                                        <ul class="float-left mt-3">
                                            <li class="li-blockchain-certificate">
                                                <template v-for="i in getLicense().getIcons('white')">
                                                    <img v-bind:src="i" alt="" />
                                                </template>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!navigation && !state.author.buzz.blocked" class="position-circle-fixed">
            <post-like-big v-on:vote="onVote"
                           v-bind:session="session"
                           v-bind:post="state.post"
                           v-bind:payouts="getPendingPayouts(state.post)">

            </post-like-big>
        </div>


    </div>
</section>
