@extends('layouts.app')

@section('content')
    <script src="js/faq/{{ __('lang.CODE') }}/faq.js"></script>
    <div class="main-container view-faq">
        <section v-cloak id="faq-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-8 order-1 order-sm-1 order-md-0 order-lg-0 ">
                        <ul class="results-list text-justify">
                            <template v-for="cat in faq.CATEGORIES">

                                <li v-for="q in faq.QUESTIONS[cat]">
                                    <h4 v-bind:id="toPermalink(q)">@{{ q }}</h4>

                                    <template v-for="r in faq.RESPONSES[cat][q]">
                                        <p v-if="linkfy(r)" v-html="linkfy(r)"></p>
                                        <p v-else v-html="r">
                                        </p>
                                    </template>

                                </li>

                            </template>

                        </ul>
                    </div>
                    <div class="col-md-pull-8 col-md-4 order-0 order-sm-0 order-md-1 order-lg-1 ">
                        <div class="boxed boxed--border boxed--lg bg--secondary">
                            <div class="sidebar__widget">
                                <template v-for="cat in faq.CATEGORIES">
                                    <h5>@{{ cat }}</h5>

                                    <ul>
                                        <li v-for="q in faq.QUESTIONS[cat]">
                                            <a v-bind:href="'#' + toPermalink(q)">@{{ q }}</a>
                                        </li>
                                    </ul>

                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>

        <script src="{{ asset('js/control/faq.js') }}"></script>

        @include('layouts.modals')

    </div>
@endsection
