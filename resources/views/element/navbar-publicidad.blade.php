<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <p class="text-white ">
                <img src="{{ asset('img/onecommet_banner.svg') }}" class="inline" style="height: 20px; margin-bottom: 5px"> - 50€ + 25k CGY -
                <a href="/@onecomet/get-crea-special-offer-25k-cgy" class="text-white">
                    {{ __('lang.NAVBAR.JOIN_NOW') }}
                </a>
                -
                <countdown v-bind:eventtime="{{ 1634335199000 }}"></countdown>
            </p>
        </div>
    </div>
</div>
<a href="#" class="close" v-on:click="closeAlert($event)"><i class="text-white fas fa-times"></i></a>
