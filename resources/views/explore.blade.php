@extends('layouts.app')

@section('content')
    <div class="main-container">
        <section id="tags-explorer" class="space--sm">
            <div v-cloak class="container post-container-home">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-explorer">{{ __('lang.TRENDING.TITLE') }}</h3>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="border--round table--alternate-row text-right">
                        <thead>
                        <tr>
                            <th>{{ __('lang.TRENDING.TAGS') }}</th>
                            <th>{{ __('lang.TRENDING.POSTS') }}</th>
                            <th>{{ __('lang.TRENDING.COMMENTS') }}</th>
                            <th>{{ __('lang.TRENDING.PAYOUTS') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="t in Object.keys(state.tags)">
                            <tr>
                                <td><a v-bind:href="'/popular/' + state.tags[t].name">@{{ state.tags[t].name }}</a></td>
                                <td>@{{ state.tags[t].top_posts }}</td>
                                <td>@{{ state.tags[t].comments }}</td>
                                <td>@{{ state.tags[t].total_payouts }}</td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>
        <script src="{{ asset('js/control/tags.js') }}"></script>

        @include('layouts.modals')
    </div>
@endsection
