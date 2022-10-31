export interface ISection {
    id: string;
    title: string;
    fields: ISectionField[];
}

export interface ISectionField {
    id: string;
    title: string;
    type: string;
    value: any;
    default?: any;
    desc?: string;
    desc_tip?: string;
    options?: ISettingOption[];
    min?: number;
    max?: number;
    step?: number;
    placeholder?: string;
    toggle?: string;
    nonce?: string;
    allow_clear?: boolean;
}

export interface ISettingOption {
    key: string;
    label: string;
    default?: string;
    tooltip?: string;
    value?: any;
}
