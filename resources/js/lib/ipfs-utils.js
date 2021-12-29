/**
 * Created by ander on 25/09/18.
 */
class IpfsFile {
    constructor(hash, name, type, size) {
        this.hash = hash;
        this.name = name;
        this.type = type;
        this.size = size;
        this.url = 'https://ipfs.creary.net/ipfs/' + hash;
    }
}

export {
    IpfsFile
}
