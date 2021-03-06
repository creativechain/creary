@extends('layouts.error')

@section('content')
    <div class="main-container view-error-404">
        <section class="height-100 bg--light text-center">
            <div class="container pos-vertical-center">
                <div class="row align-items-center">
                    <div class="col-md-6 text-left col-left">
                        <h1 class="h1--large">Oops!</h1>
                        <p class="sub-text"><span>500.</span>That’s an error.</p>
                        <p>The requested URL {{ request()->path() }} Internal Server Error.</p>
                        <p>That’s all we know.</p>
                    </div>
                    <div class="col-md-6">
                        <img src="/img/error/error.svg" alt="" />
                    </div>
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </section>
    </div>
@endsection
