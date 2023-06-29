import { IpfsFile } from './ipfs-utils';

class App {
    /**
     *
     * @param {string} name
     * @param {string} version
     */
    constructor(name, version) {
        this.name = name;
        this.version = version;
    }

    static parse(app) {
        if (app && app.name && app.version) {
            return new App(app.name, app.version);
        }

        return App.UNKNOWN;
    }
}

App.UNKNOWN = new App('unknown', 'v0.0.0');
App.CREARY = new App('creary', 'v1.1.2');

class ProfileDIP {
    /**
     *
     * @param {IpfsFile} avatar
     * @param {string} publicName
     * @param {string} about
     * @param {string} contact
     * @param {string} web
     * @param {string} lang
     * @param {object} other
     * @param {App} app
     */
    constructor(avatar, publicName = '', about = '', contact = '', web = '', lang = '', other = '', app) {
        this.avatar = avatar || new IpfsFile();
        this.public_name = publicName || '';
        this.about = about || '';
        this.contact = contact || '';
        this.web = web || '';
        this.lang = lang || '';
        this.other = other || '';
        this.app = App.parse(app);
    }

    /**
     *
     * @returns {string}
     */
    getVersion() {
        return this.app.version;
    }

    /**
     *
     * @returns {string}
     */
    getAppName() {
        return this.app.name;
    }

    static parse(metadata) {
        if (metadata) {
            return new ProfileDIP(
                metadata.avatar,
                metadata.public_name,
                metadata.about,
                metadata.contact,
                metadata.web,
                metadata.lang,
                metadata.other,
                metadata.app
            );
        }

        return ProfileDIP.EMPTY;
    }
}

ProfileDIP.EMPTY = new ProfileDIP();

class SocialLink {
    constructor(name, baseUrl, profile) {
        this.name = name;
        this.baseUrl = baseUrl;
        this.profile = profile;
        this.link = this.getLink();
    }

    /**
     *
     * @returns {string}
     */
    getLink() {
        let baseUrl = this.baseUrl || '';
        if (!baseUrl.startsWith('https://') && !baseUrl.startsWith('http://')) {
            baseUrl = `https://${baseUrl}`;
        }

        return `${baseUrl}${this.profile}`;
    }

    /**
     *
     * @returns {string}
     */
    getIconClassName() {
        return this.name.toLowerCase().replace(' ', '');
    }

    /**
     *
     * @param socObj
     * @returns {SocialLink}
     */
    static parse(socObj) {
        if (socObj) {
            return new SocialLink(socObj.name, socObj.baseUrl, socObj.profile);
        }

        return null;
    }
}

SocialLink.PERSONAL = new SocialLink('Personal', 'https://', '');
SocialLink.TWITTER = new SocialLink('Twitter', 'twitter.com/', '');
SocialLink.INSTAGRAM = new SocialLink('Instagram', 'instagram.com/', '');
SocialLink.YOUTUBE = new SocialLink('Youtube', 'youtube.com/', '');
SocialLink.VIMEO = new SocialLink('Vimeo', 'vimeo.com/', '');
SocialLink.LINKT = new SocialLink('Linkt', 'linktr.ee/', '');
SocialLink.OPENSEA = new SocialLink('Opensea', 'opensea.io/', '');
SocialLink.KNOWN_ORIGIN = new SocialLink('KnownOrigin', 'knownorigin.io/', '');
SocialLink.SUPER_RARE = new SocialLink('SuperRare', 'superrare.co/', '');
SocialLink.RARIBLE = new SocialLink('Rarible', 'rarible.com/', '');
SocialLink.MAKERSPLACE = new SocialLink('Makersplace', 'markersplace.com/', '');
SocialLink.FOUNDATION = new SocialLink('Foundation', 'foundation.app/', '');
SocialLink.ASYNC_ART = new SocialLink('Async Art', 'async.art/u/', '');
SocialLink.HIC_ET_NUNC = new SocialLink('Hice Et Nunc', 'hicetnunc.xyz/', '');
SocialLink.ETHEREUM = new SocialLink('Ethereum', 'etherscan.io/address/', '');

export { App, ProfileDIP, SocialLink };
