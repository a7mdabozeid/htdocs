// #region [Imports] ===================================================================================================

// Libraries
import React from "react";
import ICustomer from "../../types/customer";

// Actions
import { CustomerDataActions } from "../../store/actions/customers";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState, useEffect },
    antd: { Card, Form, Input, Select, Button, Popconfirm, message },
    antdIcons: { QuestionCircleOutlined  },
    redux: { bindActionCreators, connect },
} = acfwpElements;
const { Option } = Select;

const {
    adjustCustomerPoints,
    setStoreCustomerStatus,
    setStoreCustomerSources,
} = CustomerDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IForm {
    type: string;
    points: number;
}

interface IActions {
    adjustCustomerPoints: typeof adjustCustomerPoints;
    setStoreCustomerStatus: typeof setStoreCustomerStatus;
    setStoreCustomerSources: typeof setStoreCustomerSources;
}

interface IProps {
    customer: ICustomer;
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const AdjustPoints = (props: IProps) => {
    const { customer, actions } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const [form] = Form.useForm();
    const [loading, setLoading]: [boolean, any] = useState(false);
    const [showConfirm, setShowConfirm]: [boolean, any] = useState(false);
    const [maxPoints, setMaxPoints]: [number|null, any] = useState(null);

    /**
     * Validate form values before showing the confirm popup.
     */
    const validateForm = () => {

        const points = form.getFieldValue("points");

        if (!points || points < 1)
            message.error(labels.invalid_points);
        else if (maxPoints && points > maxPoints)
            message.error(labels.invalid_maxpoints.replace('{maxpoints}', maxPoints));
        else
            setShowConfirm(true);
    };

    /**
     * Get the appropriate title for the popup confirm.
     *
     * @returns {string}
     */
    const getConfirmTitle = () => {

        return labels.adjust_confirm
                .replace('{type}' , form.getFieldValue("type") === "increase" ? labels.uc_increase : labels.uc_decrease )
                .replace('{points}', form.getFieldValue("points"));
    };

    /**
     * Handle form submission for adjusting user points.
     * 
     * @since 1.0
     * @param {IForm}
     */
    const handleFormSubmit = ({ type, points }: IForm) => {

        setShowConfirm(false);
        setLoading(true);
        actions.adjustCustomerPoints({
            id: customer.id,
            type,
            points,
            successCB: (response) => {
                message.success(response.data.message);
                actions.setStoreCustomerStatus({ id: customer.id, data: response.data.status });
                actions.setStoreCustomerSources({
                    id: customer.id,
                    data: response.data.sources,
                });
            },
            failCB: ({error}) => {
                message.error(error.response.data.message);
            },
            alwaysCB: () => {
                form.resetFields();
                setMaxPoints(null);
                setLoading(false);
            }
        });
    };

    return (
        <Card className="adjust-points-card" title={labels.adjust_points}>
            <h4>{labels.adjust_for_user}</h4>
            <Form
                layout="inline"
                form={form}
                initialValues={{ type: "increase", points: 0 }}
                onFinish={handleFormSubmit}
            >
                <Form.Item name="type">
                    <Select onChange={(value: string) => {
                        const userPoints = customer?.status ? customer?.status[1].points : 0;
                        const maxValue = form.getFieldValue("type") === "decrease" ? userPoints : null;
                        setMaxPoints(maxValue);
                    }}>
                        <Select.Option value="increase">
                            {labels.increase_points}
                        </Select.Option>
                        <Select.Option value="decrease">
                            {labels.decrease_points}
                        </Select.Option>
                    </Select>
                </Form.Item>
                <Form.Item name="points">
                    <Input type="number" placeholder="points" min={0} max={maxPoints} />
                </Form.Item>
                <Form.Item>
                    <Popconfirm 
                        visible={showConfirm}
                        title={ getConfirmTitle }
                        icon={<QuestionCircleOutlined />}
                        okText={labels.proceed}
                        cancelText={labels.cancel}
                        okButtonProps={{ className:"confirm-adjust-points" }}
                        onConfirm={ () => form.submit() }
                        onCancel={ () => {
                            setShowConfirm(false);
                            form.resetFields();
                        } }
                    >
                        <Button type="primary" htmlType="button" loading={loading} onClick={ validateForm }>
                            {labels.adjust}
                        </Button>
                    </Popconfirm>
                </Form.Item>
            </Form>
        </Card>
    );
};

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators(
        {
            adjustCustomerPoints,
            setStoreCustomerStatus,
            setStoreCustomerSources,
        },
        dispatch
    ),
});

export default connect(null, mapDispatchToProps)(AdjustPoints);

// #endregion [Component]
