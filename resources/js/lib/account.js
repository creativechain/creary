/**
 * Created by ander on 27/09/18.
 */

const DEFAULT_ROLES = ['posting', 'active', 'owner', 'memo'];

class Account {

    constructor(username, keys) {
        this.username = username;
        this.keys = keys;
    }

    /**
     *
     * @returns {null|string}
     */
    getSignature() {
        let roles = Object.keys(this.keys);
        if (roles.length > 0) {
            let k = this.keys[roles[0]];
            let prvK = crea.crypto.PrivateKey.fromWif(k.prv);
            return crea.crypto.Signature.sign(this.username, prvK).toHex();
        }

        return null;
    }

    /**
     *
     * @param username
     * @param password
     * @param role
     * @returns {Account}
     */
    static generate(username, password, role = 'posting') {
        let neededRoles = [];
        let keys = {};

        if (crea.auth.isWif(password)) {

            if (role == null) {
                role = 'unknown';
            }

            keys[role] = {
                prv: password,
                pub: crea.auth.wifToPublic(password)
            };

            return new Account(username, keys);
        } else {

            let validateRole = function (r) {
                if (DEFAULT_ROLES.indexOf(r) > -1) {
                    neededRoles.push(r);
                } else {
                    throw 'Role not valid: ' + r;
                }
            };

            if (Array.isArray(role)) {
                role.forEach(function (r) {
                    validateRole(r);
                })
            } else {
                validateRole(role);
            }

            let privKeys = crea.auth.getPrivateKeys(username, password, neededRoles);

            neededRoles.forEach(function (r) {
                keys[r] = {
                    prv: privKeys[r],
                    pub: privKeys[r + 'Pubkey']
                }
            });

            return new Account(username, keys);

        }

        //TODO: LOGIN ERROR


    }
}

export { Account, DEFAULT_ROLES };
