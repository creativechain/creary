@extends('layouts.app')

@section('content')
    <div class="main-container">
        <section v-cloak id="witnesses" class="space--sm">
            <div class="container post-container-home">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="title-explorer" style="margin-bottom: 0">{{ __('lang.WITNESS.VOTE_TITLE') }}</h3>
                        <p style="margin-bottom: 1.5em" v-html="lang.WITNESS.HOW_TO">

                        </p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="border--round table--alternate-row table-vote">
                        <thead>
                        <tr>
                            <th style="width: 10%;"></th>
                            <th>{{ __('lang.WITNESS.WITNESSES') }}</th>
                            <th>{{ __('lang.WITNESS.OPERABILITY') }}</th>
                            <th>{{ __('lang.WITNESS.INFORMATION') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="x in state.ordered_witnesses">
                            <tr>
                                <td>
                                    <witness-like v-bind:index="state.ordered_witnesses.indexOf(x) + 1"
                                                  v-bind:account="account ? account.user : false"
                                                  v-bind:session="session"
                                                  v-bind:witness="state.witnesses[x]"
                                                  v-on:vote="onVote"></witness-like>
                                </td>
                                <td><a v-bind:href="'/@' + x">@{{ x }}</a></td>
                                <td v-if="state.witnesses[x].isDisabled" class="color--grey">
                                    {{  __('lang.WITNESS.DISABLED') }} (@{{ state.witnesses[x].last_block_date.fromNow() }})
                                </td>
                                <td v-else class="color--primary">
                                    {{  __('lang.WITNESS.ACTIVE') }}
                                </td>
                                <td><a v-bind:href="state.witnesses[x].url">@{{ state.witnesses[x].url }}</a> </td>
                            </tr>
                        </template>

                        </tbody>
                    </table>
                </div>

            </div>
        </section>
        <script src="{{ asset('js/control/witness.js') }}"></script>

        @include('layouts.modals')

    </div>
@endsection
