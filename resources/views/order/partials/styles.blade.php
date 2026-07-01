<style>
/* CSS Styling for optimized loading */
.action-wrap { display: flex; align-items: center; gap: 8px; }
.view-btn, .dots-btn { width: 34px; height: 34px; border: none; border-radius: 8px; background: #f3f6fb; color: #4b5563; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.order-popup-overlay, .bill-list-popup-overlay, .bill-detail-popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.28); z-index: 9999; display: none; }
.order-popup-overlay.show, .bill-list-popup-overlay.show, .bill-detail-popup-overlay.show { display: block; }
.order-popup-panel { position: absolute; top: 0; right: -100%; width: 72%; max-width: 1180px; height: 100%; background: #fff; transition: all .25s ease; overflow-y: auto; padding: 24px 28px; }
.order-popup-overlay.show .order-popup-panel { right: 0; }
.popup-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.popup-title { font-size: 19px; font-weight: 700; color: #374151; }
.popup-body-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 18px; }
.popup-card, .popup-map-card { border: 1px solid #dfe5ec; border-radius: 8px; padding: 14px; margin-bottom: 18px; background: #fff; }
.order-item-box { display: flex; gap: 14px; align-items: flex-start; }
.order-item-box img { width: 66px; height: 66px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; }
.order-item-info { flex: 1; }
.order-item-price { min-width: 120px; text-align: right; font-weight: 600; }
.discount-orange { color: #ff6a00; font-weight: 700; }
.summary-row { display: grid; grid-template-columns: 1fr 100px 120px; gap: 10px; padding: 4px 0; }
.total-row { font-weight: 700; }
.info-line { display: flex; gap: 10px; margin-bottom: 12px; font-size: 14px; }
.popup-footer { display: flex; justify-content: flex-end; gap: 12px; }
.action-dropdown { position: relative; }
.action-menu { display: none; position: absolute; right: 0; top: 40px; min-width: 170px; background: #fff; border: 1px solid #ddd; border-radius: 8px; z-index: 99999; }
.action-menu a, .action-menu button { display: block; padding: 10px 14px; width: 100%; text-align: left; background: none; border: none; cursor: pointer; text-decoration: none; color: #333; font-size: 14px; }
.bill-list-popup-panel { position: absolute; top: 0; right: -100%; width: 84%; height: 100%; background: #fff; transition: all .25s ease; }
.bill-list-popup-overlay.show .bill-list-popup-panel { right: 0; }
.bill-detail-popup-panel { position: absolute; top: 0; right: -100%; width: 450px; height: 100%; background: #fff; transition: all .25s ease; }
.bill-detail-popup-overlay.show .bill-detail-popup-panel { right: 0; }
@media print { .no-print { display: none !important; } }
</style>