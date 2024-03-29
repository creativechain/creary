/**
 * Created by ander on 21/12/18.
 */
import { clone, linkfy, cancelEventPropagation } from '../lib/util';
import Session from "../lib/session";
import { catchError } from "../common/common";

(function () {
    let roleModal;
    let alertModal;

    function setUpAlertModal() {
        let defaultData = {
            title: '',
            body: []
        };

        if (!alertModal) {
            alertModal = new Vue({
                el: '#modal-alert',
                data: {
                    lang: lang,
                    config: clone(defaultData),
                    show: false
                },
                mounted: function mounted() {
                    let that = this;
                    $('#modal-alert').on('modalClosed.modals.mr', function () {
                        that.closeModal();
                    });
                },
                methods: {
                    cleanModal: function cleanModal() {
                        this.config = clone(defaultData);
                    },
                    closeModal: function closeModal(event) {
                        cancelEventPropagation(event);
                        this.cleanModal();
                        this.show = false;
                    },
                    linkfy: linkfy
                }
            });
        }
    }

    function setUpRoleModal() {
        let defaultData = {
            username: {
                disabled: false,
                value: '',
                error: null
            },
            password: {
                value: '',
                error: null
            }
        };

        if (!roleModal) {
            roleModal = new Vue({
                el: '#modal-role',
                data: {
                    lang: lang,
                    id: null,
                    title: '',
                    role: null,
                    login: false,
                    inputs: clone(defaultData),
                    show: false
                },
                mounted: function mounted() {
                    let that = this;
                    $('#modal-role').on('modalClosed.modals.mr', function () {
                        that.closeModal();
                    });
                },
                methods: {
                    cleanModal: function cleanModal() {
                        this.inputs = clone(defaultData);
                        this.title = '';
                        this.role = '';
                        this.id = null;
                    },
                    closeModal: function closeModal(event) {
                        cancelEventPropagation(event);
                        this.cleanModal();
                        this.show = false;
                    },
                    checkUsername: function checkUsername(event) {
                        let username = event.target.value.split('/')[0];
                        let that = this;

                        if (!crea.utils.validateAccountName(username)) {
                            let accounts = [username];
                            crea.api.lookupAccountNames(accounts, function (err, result) {
                                if (err) {
                                    console.error(err);
                                    that.inputs.username.error = that.lang.ERROR.INVALID_USERNAME;
                                } else if (result[0] == null) {
                                    that.inputs.username.error = that.lang.ERROR.USERNAME_NOT_EXISTS;
                                } else {
                                    that.inputs.username.error = null;
                                }
                            });
                        } else {
                            that.inputs.username.error = that.lang.ERROR.INVALID_USERNAME;
                        }
                    },
                    fetchKey: function fetchKey(event) {
                        cancelEventPropagation(event);

                        if (!this.inputs.username.error && this.inputs.password.value) {
                            let that = this;
                            let username = this.inputs.username.value.split('/')[0];
                            let role = this.role;
                            let password = this.inputs.password.value;
                            let s = Session.create(username, password, role);
                            s.login(function (err, result) {
                                if (!catchError(err)) {
                                    if (that.login) {
                                        s.save();
                                    }

                                    creaEvents.emit('crea.auth.role.' + that.id, s.account.keys[role].prv, username);
                                    that.closeModal();
                                }
                            });
                        }
                    }
                }
            });
        }
    }

    function setUp() {
        setUpAlertModal();
        setUpRoleModal();
    }

    creaEvents.on('crea.alert', function (data) {
        //console.log(data);
        alertModal.config = data;
        alertModal.show = true;
    });
    creaEvents.on('crea.auth.role', function (username, role, login, id) {
        //Executed in timeout for modals visibility conflict
        console.log("requiring role", role)
        setTimeout(function () {
            roleModal.id = id;
            roleModal.inputs.username.value = username;
            roleModal.role = role;
            roleModal.login = login;
            roleModal.show = true;
        }, 300);
    });

    creaEvents.on('crea.content.loaded', function () {
        setUp();
    });

})();
