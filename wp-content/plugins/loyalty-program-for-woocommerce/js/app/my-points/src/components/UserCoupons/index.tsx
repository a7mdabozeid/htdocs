// #region [Imports] ===================================================================================================

// Libraries
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { Table, Pagination, Button, Skeleton } from "antd";

// Actions
import { UserCouponsActions } from "../../store/actions/coupons";

// Types
import IStore from "../../types/store";
import IUserCoupon from "../../types/coupons";

// Helpers
import { getPathPrefix } from "../../helpers/utils";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;

const { readUserCoupons } = UserCouponsActions;
const pathPrefix = getPathPrefix();

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
    readUserCoupons: typeof readUserCoupons;
}

interface IProps {
    coupons: IUserCoupon[];
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const UserCoupons = (props: IProps) => {
    const { coupons, actions } = props;
    const { cart_url, labels } = lpfwMyPoints;
    const [loadCoupons, setLoadCoupons]: [boolean, any] = useState(false);
    const [loading, setLoading]: [boolean, any] = useState(false);
    const [currentPage, setCurrentPage]: [number, any] = useState(1);
    const [total, setTotal]: [number, any] = useState(0);

    useEffect(() => {
        actions.readUserCoupons({
            page: 1,
            successCB: (response) => {
                setTotal(response.headers["x-total"]);
                setLoadCoupons(true);
            },
        });
    }, []);

    if (!loadCoupons) return <Skeleton active />;

    const columns = [
        {
            title: `${labels.coupon_code}`,
            dataIndex: "code",
            key: "code",
        },
        {
            title: `${labels.amount}`,
            dataIndex: "amount",
            key: "amount",
        },
        {
            title: `${labels.redeem_date}`,
            dataIndex: "date_created",
            key: "date_created",
        },
        {
            title: `${labels.expire_date}`,
            dataIndex: "date_expire",
            key: "date_expire",
        },
        {
            title: `${labels.action}`,
            dataIndex: "code",
            key: "code",
            render: (value: string, record: IUserCoupon) => (
                <Button
                    href={`${cart_url}?lpfw_coupon=${record.code}`}
                    type="primary"
                >
                    {labels.apply_coupon}
                </Button>
            ),
        },
    ];

    /**
     * Handle pagination click event.
     *
     * @param page
     */
    const handlePagination = (page: number) => {
        setCurrentPage(page);
        setLoading(true);
        actions.readUserCoupons({
            page: page,
            successCB: () => setLoading(false),
        });
    };

    return (
        <div className="user-coupons">
            <h3>{labels.reward_coupons}</h3>
            {coupons.length ? (
                <>
                    <div className="redeem-points">
                        <p>
                            <Link to={`${pathPrefix}lpfw-my-points/?tab=redeem`}>
                                {labels.click_to_redeem} 
                            </Link>
                        </p>
                    </div>
                    <Table
                        loading={loading}
                        pagination={false}
                        dataSource={coupons}
                        columns={columns}
                    />
                    {total ? (
                        <Pagination
                            defaultCurrent={currentPage}
                            hideOnSinglePage={true}
                            current={currentPage}
                            total={total}
                            pageSize={10}
                            showSizeChanger={false}
                            onChange={handlePagination}
                        />
                    ) : null}
                </>
            ) : (
                <p>
                    {labels.no_coupons_found}{" "}
                    <Link to={`${pathPrefix}lpfw-my-points/?tab=redeem`}>
                        {labels.click_to_redeem}
                    </Link>
                </p>
            )}
        </div>
    );
};

const mapStateToProps = (store: IStore) => ({
    coupons: store.coupons,
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
    actions: bindActionCreators({ readUserCoupons }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(UserCoupons);

// #endregion [Component]
