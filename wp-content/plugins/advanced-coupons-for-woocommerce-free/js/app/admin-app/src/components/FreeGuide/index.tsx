// #region [Imports] ===================================================================================================

// Libraries
import React, { useState } from "react";
import { Tag, Button, Input, message } from "antd";
import { BulbFilled } from "@ant-design/icons";

// CSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] ================================================================================================

declare var acfwAdminApp: any;
declare var ajaxurl: string;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    className?: string;
    showSubtitle?: boolean;
    showTag?: boolean;
}

// #endregion [Interfaces]

// #region [Component] ================================================================================================

const FreeGuide = (props: IProps) => {

    const { className, showSubtitle, showTag } = props;
    const { free_guide: {
        show,
        tag,
        title,
        subtitle,
        content,
        image,
        button,
        list,
        field_values,
        placeholders,
        form_nonce,
        missing_form_fields,
        failed_form_error
    } } = acfwAdminApp;

    const [formName, setFormName] = useState(field_values.name ?? '');
    const [formEmail, setFormEmail] = useState(field_values.email ?? '');
    const [loading, setLoading] = useState(false);
    const [showForm, setShowForm] = useState(show);

    const handleSubscribeForm = async (e: any) => {

        e.preventDefault();

        if (!formName || !formEmail) {
            message.error(missing_form_fields);
            return;
        }

        setLoading(true);

        const date = new Date();
        const formData = new FormData();

        formData.append('action', 'acfwf_get_free_training_guide');
        formData.append('name', formName);
        formData.append('email', formEmail);
        formData.append('title', document.title);
        formData.append('url', window.location.href);
        formData.append('referrer', document.referrer);
        formData.append('timestamp', date.getTime().toString());
        formData.append('nonce', form_nonce);

        try {
            
            const response = await fetch(ajaxurl, {
                method: "post",
                body: formData
            });

            const data = await response.json();

            if ('success' === data.status) {
                message.success(data.message);
                acfwAdminApp.free_guide.show = false;
                setShowForm(false);
            } else {
                message.error(data.error_msg);
            }

        } catch (e) {
            message.error(failed_form_error);
        }

        setLoading(false);
    };

    if (!showForm) {
        return null;
    }

    return (
        <div className={`coupons-free-guide ${ className }`}>
            <div className="inner">
                <form onSubmit={handleSubscribeForm}>
                    { showTag ? <Tag color="#1693A7">{ tag }</Tag> : null }
                    <h2>{ title }</h2>
                    <img src={ image } alt={ title } />
                    { showSubtitle ? <h3>{ subtitle }</h3> : null }
                    <p dangerouslySetInnerHTML={{ __html: content }} />
                    <ul>
                        { list.map( (list_text: string, index: number) => (
                            <li key={ index }>
                                <BulbFilled />
                                { list_text }
                            </li>
                        ) ) }
                    </ul>
                    <p className="form-fields">
                        <Input value={formName} onChange={(e) => setFormName(e.target.value)} placeholder={placeholders.name} />
                        <Input value={formEmail} type="email" onChange={(e) => setFormEmail(e.target.value)} placeholder={placeholders.email} />
                    </p>
                    <p className="form-actions">
                        <Button
                            type="link"
                            htmlType="submit"
                            className="cta"
                            size="large"
                            loading={loading}
                        >
                            { button.text }
                        </Button>
                    </p>
                </form>
            </div>
        </div>
    );
};

FreeGuide.defaultProps = {
    className: '',
    showSubtitle: false,
    showTag: true,
};

export default FreeGuide;

// #endregion [Component]