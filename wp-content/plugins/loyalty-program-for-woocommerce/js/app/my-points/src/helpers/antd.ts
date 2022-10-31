// #region [Imports] ===================================================================================================

// Libraries
import enUS from "antd/lib/locale/en_US";
import enGB from "antd/lib/locale/en_GB";
import frFR from "antd/lib/locale/fr_FR";
import ruRU from "antd/lib/locale/ru_RU";
import ptBR from "antd/lib/locale/pt_BR";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;

// #endregion [Variables]

// #region [Functions] =================================================================================================

export const getAntdLocale = () => {
    const { app_lang } = lpfwMyPoints;

    if ("en_GB" === app_lang) {
        return enGB;
    }

    if (app_lang.includes("fr_")) {
        return frFR;
    }

    if (app_lang.includes("_RU")) {
        return ruRU;
    }

    if (app_lang.includes("pt_")) {
        return ptBR;
    }

    return enUS;
};

// #endregion [Functions]
