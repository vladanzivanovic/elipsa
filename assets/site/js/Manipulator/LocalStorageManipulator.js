class LocalStorageManipulator {
    constructor() {
        if(!LocalStorageManipulator.instance) {
            LocalStorageManipulator.instance = this;
        }

        return LocalStorageManipulator.instance;
    }

    setItem(itemName, data)
    {
        localStorage.setItem(itemName, data);
    }

    getItem(itemName)
    {
        return localStorage.getItem(itemName);
    }

    removeItem(itemName)
    {
        localStorage.removeItem(itemName);
    }
}

const localStorageManipulator = new LocalStorageManipulator();

Object.freeze(localStorageManipulator);

export default localStorageManipulator;
