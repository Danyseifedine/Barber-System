export class Identifiable {
    constructor(identifier) {
        if (new.target === Identifiable) {
            throw new TypeError("Cannot construct Identifiable instances directly");
        }
        this.identifier = identifier;
    }

    getIdentifier() {
        return this.identifier;
    }
}
