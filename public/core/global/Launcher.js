import { SingleFormPostHandler } from "./handlers/singleFormPostHandler.js";
import { TabHandler } from "./handlers/tabHandler.js";
import { CounterHandler } from "./handlers/counterHandler.js";
import { ModalHandler } from "./handlers/modalHandler.js";
import { identifiers, env } from "./config/app-config.js";
import { DevTools } from "./devtools/dev.tools.js";
import { l10n } from "./config/app-config.js";

export class Launcher {
    static componentMap = {
        [identifiers.singleFormPost]: SingleFormPostHandler,
        [identifiers.tabHandler]: TabHandler,
        [identifiers.counterHandler]: CounterHandler,
        [identifiers.modalHandler]: ModalHandler,
    };

    static initializedComponents = new Set();

    static init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeComponents();
            this.initializeDevTools();
        });
    }

    static initializeComponents() {
        Object.entries(this.componentMap).forEach(([identifier, Component]) => {
            if (document.querySelector(`[identifier="${identifier}"]`)) {
                if (!this.initializedComponents.has(identifier)) {
                    new Component(identifier);
                    this.initializedComponents.add(identifier);
                }
            }
        });
    }

    static initializeDevTools() {
        if (env.isDevelopment && env.enableDevTools) {
            DevTools.init(this.componentMap, this.initializedComponents);
        }
    }
}

Launcher.init();
