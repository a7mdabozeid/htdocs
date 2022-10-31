// #region [Imports] ===================================================================================================

// Libraries
import { useState } from "react"; 
import { bindActionCreators, Dispatch } from "redux";
import { Input, Select, Switch, Button } from "antd";
import { FilterOutlined } from "@ant-design/icons";
import { connect } from "react-redux";

// Actions
import {VirtualCouponQueryActions} from "../../../../store/actions/virtualCouponQuery";

// Types
import { IVirtualCouponQuery } from "../../../../types/virtualCouponQuery";
import IStore from "../../../../types/store";

// Helpers
import {getAppLabels} from "../../../../helpers/utils";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Interfaces] ================================================================================================

interface IProps {
  query: IVirtualCouponQuery;
  actions: typeof VirtualCouponQueryActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const FilterVirtualCoupons = (props: IProps) => {
  const { query, actions } = props;
  const labels = getAppLabels();
  const [search, setSearch]:[string, any] = useState('');
  const [status, setStatus]:[string, any] = useState('');
  const [fresh, setFresh]: [boolean, any] = useState(query?.date_created === "recent");

  /**
   * Filter query event on button click.
   */
  const handleFilterQuery = () => {
    actions.setSearch(search);
    actions.setStatus(status);
    actions.setDateCreated(fresh ? 'recent' : '');
    actions.setPage(1);
  };

  return (
    <div className="virtual-coupons-filter">
      <Input 
        allowClear
        className="search-filter"
        placeholder={labels.search_hellip}
        value={search} 
        onChange={(e) => setSearch(e.target.value)}
      />
      <Select 
        allowClear
        className="status-filter"
        placeholder="Select a status" 
        onSelect={setStatus}
        onClear={() => setStatus('')}
      >
        <Select.Option value="pending">{labels.pending}</Select.Option>
        <Select.Option value="used">{labels.used}</Select.Option>
      </Select>
      <Switch 
        checked={fresh} 
        onChange={setFresh}
        checkedChildren={labels.recent}
        unCheckedChildren={labels.recent}
      />
      <Button 
        type="primary"
        onClick={handleFilterQuery}
      >
        <FilterOutlined />
        {labels.filter}
      </Button>
    </div>
  );
};

const mapStateToProps = (store: IStore) => ({
  query: store.virtualCouponQuery,
});
const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({...VirtualCouponQueryActions}, dispatch),
});

export default connect(mapStateToProps,mapDispatchToProps)(FilterVirtualCoupons);

// #endregion [Component]
