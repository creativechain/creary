<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <p class="text-white ">
                <img src="{{ asset('img/onecommet_banner.svg') }}" class="inline" style="height: 20px; margin-bottom: 5px"> - 50€ + 20k CGY -
                <a href="/crea/@onecomet/20k-cgy-get-crea-special-offer" class="text-white">
                    {{ __('lang.NAVBAR.JOIN_NOW') }}
                </a>
                -
                <countdown v-bind:eventtime="{{ 1628459999000 }}"></countdown>
            </p>
        </div>
    </div>
</div>
<a href="#" class="close" v-on:click="closeAlert($event)"><i class="text-white fas fa-times"></i></a>
