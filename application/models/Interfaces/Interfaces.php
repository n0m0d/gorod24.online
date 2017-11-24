<?php

class Payment_Interface {
    protected $_iterator = null;
    protected $_name = null;
    protected $_descr = null;
    protected $_settings = null;
                /*
                 * Настроки - используется для построение формы в админ-панеле для указания настроек
                 * array(
                 *      'SETTING-1-KEY'     => array(
                 *          'label'     => 'Название параметра',
                 *          'type'      => [text|password|int|double|check|list|textarea|multilist']   - тип данных (какой элемент будет в форме)
                 *          'items'     => array(           - опционально - только для типов list и multilist
                 *              'val-1-key' => 'val-1-label',
                 *              'val-2-key' => 'val-2-label',
                 *                  ...
                 *              'val-n-key' => 'val-n-label'
                 *          ),
                 *          'default'   => Значение по умолчанию
                 *          'require'   => [true|false] - обязательное поле или нет
                 *      ),
                 *      'SETTING-2-KEY'     => array( ... )
                 *      ......
                 *      'SETTING-N-KEY'     => array( ... )
                 * )
                 */
    protected $_configed_settings = null;
    protected $_fields = null;
    protected $_logo_small = null;
    protected $_logo_medium = null;
    protected $_logo_big = null;
    protected $_last_error = false;
    protected $_is_disabled    = false;
    
    
    public function __construct(&$iterator) {
        $this->_iterator = $iterator;
        $this->_configed_settings = $iterator->getInterfaceSettings($this->_name);
        foreach ($this->_settings as $k=>&$conf) {
            if (!isset($this->_configed_settings[$k])) {
                if (isset($conf['default'])) {
                    $this->_configed_settings[$k] = $conf['default'];
                } else {
                    $this->_configed_settings[$k] = null;
                }
            }
        }
    }
    /**
     * 
     */
    public function is_disabled() {
        return $this->_is_disabled;
    }
    /**
     * 
     */
    public function get_configed_settings() {
        return $this->_configed_settings;
    }
    public function admin_get_transaction_toolbar($tr_id) {
        return '';
    }
    /**
     * 
     */
    public function admin_get_toolbar() {
        return '';
    }
    /**
     * 
     * @return type
     */
    public function get_name() {
        return $this->_name;
    }
    /**
     * 
     * @return type
     */
    public function get_descr() {
        return $this->_descr;
    }
    /**
     * 
     * @return type
     */
    public function get_settings() {
        return $this->_settings;
    }
    /**
     * 
     * @param type $invoice_id
     * @param type $invoice_price
     * @return type
     */
    public function get_fields($invoice_id,$invoice_price) {
        return $this->_fields;
    }
    /**
     * 
     * @return type
     */
    public function get_logo_small() {
        return $this->_logo_small;
    }
    /**
     * 
     * @return type
     */
    public function get_logo_medium() {
        return $this->_logo_medium;
    }
    /**
     * 
     */
    public function get_logo_big() {
        return $this->_logo_big;
    }
    /**
     * 
     * @param type $in_fields
     */
    public function calc_fields($in_fields) {
    }
    /**
     * 
     * @param type $transaction_id
     * @param type $transaction_price
     * @param type $request
     * @return type
     */
    public function transaction_start($transaction_id,$transaction_price,$transaction_desc,$request) {
        return array(
            'fields'    => array(
                'id'    => $transaction_id,
                'price' => $transaction_price,
            ),
            'target'    => 'http://payment-provider.com'
        );
    }
    /**
     * 
     * @param type $request
     */
    public function transaction_fail($request) {
    }
    /**
     * 
     * @param type $request
     */
    public function transaction_success($request) {
    }
    /**
     * 
     * @param type $transaction_data
     */
    public function transaction_complete($request) {
    }
    /**
     * 
     * @return type
     */
    public function get_error() {
        return $this->_last_error;
    }
}
?>