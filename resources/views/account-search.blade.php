@extends('layouts.app')

@section('content')


    <div class="main-container view-profile">
        <section class="bg--secondary p-top-15">
            <div id="accounts-list" class="container">
                <div class="row justify-content-center">
                    <div v-cloak class="col-12 col-lg-9 col-xl-9">
                        <section class="space--sm">
                            <div class="container">
                                @include('modules.list-accounts')
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

        <script src="{{ asset('js/control/accounts-list.js') }}"></script>

        @include('layouts.modals')

    </div>

@endsection
