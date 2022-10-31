// #region [Variables] =================================================================================================
declare var axios: any;

declare var wpApiSettings: any;
const CancelToken = axios.CancelToken;

// #endregion [Variables]

export default axios.create({
  baseURL: wpApiSettings.root,
  timeout: 0,
  headers: { "X-WP-Nonce": wpApiSettings.nonce, "X-ACFW-Context": "admin" },
});

// variable to save all axios cancels.
const axiosCancelMap = new Map();

// export axios cancel method.
export const axiosCancel = (id: string) => {
  const cancel = axiosCancelMap.get(id);
  if (cancel) {
    cancel();
    axiosCancelMap.delete(id);
  }
};

// export cancel token.
export const getCancelToken = (id: string) =>
  new CancelToken((c: any) => axiosCancelMap.set(id, c));
