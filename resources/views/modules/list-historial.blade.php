<div v-if="walletTab === 'balances'" class="boxed boxed--border no-padding">
    <div class="col-md-12">
        <h3 class="title-section-profile title-list-historial" >{{ __('lang.WALLET.HISTORY') }}</h3>
    </div>
    <div class="col-md-12" style="overflow: hidden;">
        <hr class="mb-0">
    </div>
    <template v-for="op in history.data">
        <div v-if="op.op.type === 'transfer_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar cursor-link" v-on:click="showProfile(op.op.value.from)">
                                <avatar v-bind:account="history.accounts[op.op.value.from]"></avatar>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.from" v-bind:name="history.accounts[op.op.value.from].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>
                                <p v-if="account && op.op.value.from === account.user.name">
                                    {{ __('lang.HISTORY.TRANSFER_TO') }}
                                    <linkname v-bind:user="op.op.value.to" v-bind:name="history.accounts[op.op.value.to].metadata.publicName"></linkname>
                                </p>
                                <p v-else>
                                    {{ __('lang.HISTORY.TRANSFER_FROM') }}
                                    <linkname v-bind:user="op.op.value.from" v-bind:name="history.accounts[op.op.value.from].metadata.publicName"></linkname>
                                </p>
                                <p>@{{ op.op.value.memo || "" }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p v-if="account && op.op.value.from === state.user.name">-@{{ parseAsset(op.op.value.amount, null, false) }}</p>
                            <p v-else >+@{{ parseAsset(op.op.value.amount, null, false) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'transfer_to_savings_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar cursor-link" v-on:click="showProfile(op.op.value.from)">
                                <avatar v-bind:account="history.accounts[op.op.value.from]"></avatar>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.from" v-bind:name="history.accounts[op.op.value.from].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>
                                <p v-if="account && op.op.value.from === account.user.name">
                                    {{ __('lang.HISTORY.TRANSFER_SAVINGS_TO') }}
                                    <linkname v-bind:user="op.op.value.to" v-bind:name="history.accounts[op.op.value.to].metadata.publicName"></linkname>
                                </p>
                                <p v-else>
                                    {{ __('lang.HISTORY.TRANSFER_SAVINGS_FROM') }}
                                    <linkname v-bind:user="op.op.value.from" v-bind:name="history.accounts[op.op.value.from].metadata.publicName"></linkname>
                                </p>
                                <p>@{{ op.op.value.memo || "" }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p v-if="account && op.op.value.from === state.user.name">-@{{ parseAsset(op.op.value.amount, null, false) }}</p>
                            <p v-else >+@{{ parseAsset(op.op.value.amount, null, false) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'transfer_to_vesting_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.from">
                                    <avatar v-bind:account="history.accounts[op.op.value.from]"></avatar>
                                </a>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.from" v-bind:name="history.accounts[op.op.value.from].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>

                                <p v-if="account && op.op.value.from === state.user.name">
                                    {{ __('lang.HISTORY.TRANSFER_VESTING_TO') }}
                                    <linkname v-bind:user="op.op.value.to" v-bind:name="history.accounts[op.op.value.to].metadata.publicName"></linkname>
                                </p>
                                <p v-else >
                                    {{ __('lang.HISTORY.TRANSFER_VESTING_FROM') }}
                                    <linkname v-bind:user="op.op.value.from" v-bind:name="history.accounts[op.op.value.from].metadata.publicName"></linkname>
                                </p>

                                <p>@{{ op.op.value.memo || "" }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p v-if="account && op.op.value.from === state.user.name">+@{{ parseAsset(op.op.value.amount, null, false) }}</p>
                            <p v-else >+@{{ parseAsset(op.op.value.amount, null, false) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'comment_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.author">
                                    <avatar v-bind:account="history.accounts[op.op.value.author]"></avatar>
                                </a>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.author" v-bind:name="history.accounts[op.op.value.author].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>

                                <p v-if="op.op.value.parent_author != ''">
                                    {{ __('lang.HISTORY.COMMENTED') }} @{{ op.op.value.parent_permlink }}
                                </p>
                                <p v-else >
                                    {{ __('lang.HISTORY.POSTED') }} @{{ op.op.value.title }}
                                </p>
                                <p>@{{ op.op.value.parent_author != "" ? op.op.value.body : "" }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p v-if="account && op.op.value.from === state.user.name">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'comment_download_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.downloader">
                                    <avatar v-bind:account="history.accounts[op.op.value.downloader]"></avatar>
                                </a>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.downloader" v-bind:name="history.accounts[op.op.value.downloader].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>

                                <p >
                                    {{ __('lang.HISTORY.COMMENT_DOWNLOAD') }} @{{ op.op.value.comment_permlink }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'vote_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.voter">
                                    <avatar v-bind:account="history.accounts[op.op.value.voter]"></avatar>
                                </a>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.voter" v-bind:name="history.accounts[op.op.value.voter].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>
                                <p>
                                    {{ __('lang.HISTORY.VOTED_FOR') }} @{{ op.op.value.permlink }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p>@{{ (op.op.value.weight * 100 / 10000).toFixed(0) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'producer_reward_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.producer">
                                    <avatar v-bind:account="history.accounts[op.op.value.producer]"></avatar>
                                </a>
                            </div>

                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.producer" v-bind:name="history.accounts[op.op.value.producer].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>
                                <p>
                                    @{{ String.format(lang.HISTORY.PRODUCED, parseAsset(op.op.value.vesting_shares, null, false)) }}
                                </p>
                                <p></p>
                            </div>

                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p>+@{{ parseAsset(op.op.value.vesting_shares, null, false) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'curator_reward_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.curator">
                                    <avatar v-bind:account="history.accounts[op.op.value.curator]"></avatar>
                                </a>
                            </div>

                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.curator"
                                              v-bind:name="history.accounts[op.op.value.curator].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>
                                <p>
                                    <linkname v-bind:user="op.op.value.curator"
                                              v-bind:name="history.accounts[op.op.value.curator].metadata.publicName"></linkname>
                                    @{{ String.format(lang.HISTORY.CURATE, parseAsset(op.op.value.reward), "/@" + op.op.value.comment_author + "/"+ op.op.value.comment_permlink) }}
                                </p>
                                <p></p>
                            </div>

                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p>+@{{ parseAsset(op.op.value.reward, null, false) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="op.op.type === 'account_create_operation'" class="row-list-user">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-8 col-md-8">
                        <div class="row-list-user-display">
                            <div class="user-avatar">
                                <a v-bind:href="'/@' + op.op.value.creator">
                                    <avatar v-bind:account="history.accounts[op.op.value.creator]"></avatar>
                                </a>
                            </div>
                            <div class="list-data-user">
                                <p>
                                    <username v-bind:user="op.op.value.creator" v-bind:name="history.accounts[op.op.value.creator].metadata.publicName"></username>
                                    <span>@{{ dateFromNow(op.timestamp) }}</span>
                                </p>
                                <p>
                                    <linkname v-bind:user="op.op.value.creator" v-bind:name="history.accounts[op.op.value.creator].metadata.publicName"></linkname>
                                    {{ __('lang.HISTORY.CREATE_ACCOUNT') }} @{{ op.op.value.new_account_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <div class="list-amount">
                            <p>@{{ parseAsset(op.op.value.fee, null, false) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
