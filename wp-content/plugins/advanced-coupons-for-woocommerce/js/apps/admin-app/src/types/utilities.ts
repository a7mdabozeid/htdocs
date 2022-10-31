
export interface IUtilityCard {
    title: string,
    desc: string,
    nonce: string,
    id: string,
    buttons: IUtilityAction[]
}

export interface IUtilityAction {
    text: string;
    action: string;
    type: string;
}