(this.webpackJsonp=this.webpackJsonp||[]).push([["event-candy-packlist"],{"1nD5":function(e,t){e.exports='{% block sw_order_document_settings_modal_form_document_number %}\n    <sw-text-field :label="$tc(\'sw-order.documentModal.labelDocumentNumber\')"\n                   v-model="documentConfig.documentNumber">\n    </sw-text-field>\n{% endblock %}'},uw8a:function(e,t,n){"use strict";n.r(t);var o=n("1nD5"),c=n.n(o);const{Component:m}=Shopware;m.extend("sw-order-document-settings-packlist-modal","sw-order-document-settings-modal",{template:c.a,created(){this.createdComponent()},methods:{onCreateDocument(e=!1){this.documentNumberPreview===this.documentConfig.documentNumber?this.numberRangeService.reserve("document_"+this.currentDocumentType.technicalName,this.order.salesChannelId,!1).then(t=>{this.documentConfig.custom.packlistNumber=t.number,this.callDocumentCreate(e)}):(this.documentConfig.custom.packlistNumber=this.documentConfig.documentNumber,this.callDocumentCreate(e))},onPreview(){this.documentConfig.custom.packlistNumber=this.documentConfig.documentNumber,this.$super("onPreview")}}})}},[["uw8a","runtime"]]]);