/**
 * Created by ander on 16/10/18.
 */

import R from './resources';

class LicensePermission {

    /**
     *
     * @param {number} flag
     * @param {string} name
     * @param {string} tag
     * @param {Object} icons
     * @param {boolean} showName
     */
    constructor(flag, name, tag, icons, showName = true) {
        this.flag = flag;
        this.name = name;
        this.tag = tag;
        this.icons = icons;
        this.showName = showName;
    }

    getIcon(color = 'BLACK') {
        color = color.toUpperCase();
        return this.icons[color];
    }
}

class License {

    /**
     *
     * @param {...} licensePermissions
     */
    constructor(...licensePermissions) {
        this.licensePermissions = licensePermissions || [];
    }

    /**
     *
     * @param {LicensePermission} licensePermission
     * @param {number} index
     */
    add(licensePermission, index = -1) {
        if (index > -1) {
            this.licensePermissions.splice(index, 0, licensePermission);
        } else {
            this.licensePermissions.push(licensePermission)
        }
    }

    /**
     *
     * @param {LicensePermission} licensePermission
     */
    remove(licensePermission) {
        let index = this.licensePermissions.indexOf(licensePermission);
        if (index > -1) {
            this.licensePermissions.splice(index, 1);
        }
    }

    clear() {
        this.licensePermissions.splice(0, this.licensePermissions.length);
    }

    /**
     *
     * @param color
     * @returns {Array}
     */
    getIcons(color) {
        let icons = [];
        this.licensePermissions.forEach(function (perm) {
            icons.push(perm.getIcon(color))
        });

        return icons;
    }

    getFlag() {
        let flag = 0;
        this.licensePermissions.forEach(function (perm) {
            flag = flag | perm.flag;
        });

        return flag;
    }

    /**
     *
     * @returns {string}
     */
    getLink() {
        return LICENSE_LINKS[this.getFlag()];
    }

    /**
     *
     * @param flag
     * @returns {boolean}
     */
    has(flag) {
        let  flags = this.getFlag();
        return flag === (flags & flag);
    }

    /**
     *
     * @returns {boolean}
     */
    isCreativeCommons() {
        return this.has(0x01);
    }

    /**
     *
     * @returns {string}
     */
    toString() {
        let str = '';
        this.licensePermissions.forEach(function (perm) {
            if (perm.showName) {
                if (!str.isEmpty()) {
                    str += ' + ';
                }

                str += perm.name;
            }
        });

        return str;
    }

    /**
     *
     * @returns {string}
     */
    getTags() {
        let tags = [];
        this.licensePermissions.forEach(function (perm) {
            tags.push(perm.tag)
        });

        return (this.isCreativeCommons() ? 'Creative Commons ' : '') +  tags.join('-');
    }

    toLocaleString() {
        let str = '';
        this.licensePermissions.forEach(function (perm) {
            if (perm.showName) {
                if (!str.isEmpty()) {
                    str += '-';
                }

                str += lang.LICENSE[perm.name.toUpperCase()];
            }
        });

        return str;
    }

    /**
     *
     * @param flag
     * @returns {License}
     */
    static fromFlag(flag) {

        if (flag === 0) {
            return new License(LICENSE.NO_LICENSE);
        } else {
            let license = new License();

            let checkPerm = function (perm) {
                let f = flag & perm.flag;
                if (f === perm.flag) {
                    license.add(perm);
                }
            };

            checkPerm(LICENSE.CREATIVE_COMMONS);
            checkPerm(LICENSE.ATTRIBUTION);
            checkPerm(LICENSE.NON_COMMERCIAL);
            checkPerm(LICENSE.SHARE_ALIKE);
            checkPerm(LICENSE.NON_DERIVATES);
            checkPerm(LICENSE.NON_PERMISSION);
            checkPerm(LICENSE.FREE_CONTENT);

            return license;
        }
    }
}

let LICENSE = {
    NO_LICENSE: new LicensePermission(0x00, 'WithoutLicense', 'WL', {}),
    CREATIVE_COMMONS: new LicensePermission(0x01, 'Creative Commons', 'CC', R.IMG.LICENSE.CREATIVE_COMMONS, false),
    ATTRIBUTION: new LicensePermission(0x02, 'Attribution', 'BY', R.IMG.LICENSE.ATTRIBUTION),
    SHARE_ALIKE: new LicensePermission(0x04, 'ShareAlike', 'SA', R.IMG.LICENSE.SHARE_ALIKE),
    NON_COMMERCIAL: new LicensePermission(0x08, 'NonCommercial', 'NC', R.IMG.LICENSE.NON_COMMERCIAL),
    NON_DERIVATES: new LicensePermission(0x10, 'NonDerivates', 'ND', R.IMG.LICENSE.NON_DERIVATES),
    NON_PERMISSION: new LicensePermission(0x20, lang.LICENSE.NON_PERMISSION, lang.LICENSE.NON_PERMISSION, R.IMG.LICENSE.COPYRIGHT),
    FREE_CONTENT: new LicensePermission(0x80, 'Free Content', 'CC0', R.IMG.LICENSE.FREE_CONTENT),
};

let LICENSE_LINKS = {
    3: 'https://creativecommons.org/licenses/by/4.0/',
    7: 'https://creativecommons.org/licenses/by-sa/4.0/',
    11: 'https://creativecommons.org/licenses/by-nc/4.0/',
    15: 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
    19: 'https://creativecommons.org/licenses/by-nd/4.0/',
    27: 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
    129: 'https://creativecommons.org/publicdomain/zero/1.0/',
};

export {
    LicensePermission, License, LICENSE, LICENSE_LINKS
};
