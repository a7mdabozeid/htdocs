// #region [Interfaces] ================================================================================================

export interface IEditingVirtualCoupon {
  key: string;
  id: number;
  code: string;
  status: string;
  date_created: string;
  date_expire: string;
  user_id: number;
}

interface IAction {
  type: string;
  value: IEditingVirtualCoupon | string | number;
}

// #endregion [Interfaces]

const editingReducer = (state: IEditingVirtualCoupon, action: IAction) => {
  switch (action.type) {
    case "all":
      return action.value as IEditingVirtualCoupon;
    case "reset":
      return {
        key: "",
        id: 0,
        code: "",
        status: "pending",
        date_created: "",
        date_expire: "",
        user_id: 0,
      };
    case "code":
      return { ...state, code: action.value as string };
    case "status":
      return { ...state, status: action.value as string };
    case "date_created":
      return { ...state, date_created: action.value as string };
    case "date_expire":
      return { ...state, date_expire: action.value as string };
    case "user_id":
    case "user":
      const userID = action.value ? parseInt(action.value.toString()) : 0;
      return { ...state, user_id: userID };
    case "key":
      return { ...state, key: action.value as string };
  }

  return state;
};

export default editingReducer;
