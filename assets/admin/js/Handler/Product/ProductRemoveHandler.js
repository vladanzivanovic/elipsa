import AdsDataTables from "../../Services/DataTables/ProductDataTables";
import AdsEditRest from "../../Rest/AdsEditRest";

export default (() => {
    let Public = {};

    Public.remove = (alias) => {
       // AdsEditRest.removeAd(alias).
       // then(response => {
       //     AdsDataTables().reload();
       // }).
       // fail(errors => {
       //     console.log(errors);
       // });
    };

    return Public;
})();