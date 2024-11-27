var vueModal = function(options = {}){
    var globalOptions = Object.assign({}, options);
    return vueModalInitialize(globalOptions);
};

var vueModalInitialize = function(options={}){
    var modalOuter, modalDialog, modalContent, modalContentObject, modalHtmlContent;
    var vueIntance = new Vue();
    var defaultConfig = {
        id: "tempVueModalContainer",
        setSize: false, /* Text Format */
        setContent: "", /* Object { ajax } OR { params: { title: "text", sub_title: "text", close_header: true, content: "text/html" } } / HTML */
        setDialogId: false, /* Text Format */
        setContentId: false, /* Text Format */
        drawCallback: function(){},
        initVueInstance: function(){},
    };

    var config = Object.assign({}, defaultConfig, options);
    config.count = 0;
    modalOuter = $('<div/>', {
        id: 'tempVueModalContainer',
        class: 'modal fade',
        tabindex: '-1',
        role: 'dialog',
    });

    var setModalComponent = function(components={}){
        var objectComponents = {};
        var keys = Object.keys(components);
        $.each(keys, function(i, v){
            var currentComponent = components[v];
            var currentObject = $('<'+v+'/>');
            if(typeof currentComponent.props !== "undefined" && typeof currentComponent.props == "object"){
                console.log(currentComponent.props);
                $.each(currentComponent.props, function(ii, vv){
                    currentObject.prop("v-bind:"+vv, vv);
                });
            }
            console.log(currentObject);
            objectComponents = Object.assign({}, objectComponents, { [v]: currentObject  });
        });

        return objectComponents;
    }

    var setModalTitle = function(title, subTitle, hasModalClose=true){
        var modalHeader = $('<div/>', { class: 'modal-header', });
        var headerTitle = $('<h5/>', { class: 'modal-title', text: 'Modal Title Example' });
        if(title){ headerTitle.text(title); }
        if(subTitle){ 
            var modalSubTitle = $('<small/>', { class: 'modal-sub_title', text: subTitle });
            modalSubTitle.appendTo(headerTitle);
        }
        headerTitle.appendTo(modalHeader);
        if(hasModalClose){
            var headerAction = $('<button/>', { class: 'close', "data-dismiss":"modal", "arial-label": 'Close' });
            headerAction.appendTo(modalHeader);
        }

        return modalHeader;
    }

    var setModalContent = function(content){
        var modalContent = $('<div/>', { class: 'modal-body' });
        if(content){ modalContent.empty().html(content); }
        else{ modalContent.empty().text("No Modal Content Preview!"); }

        return modalContent;
    }

    modalDialog = $('<div/>', { class: "modal-dialog", role: "document" });
    modalContent = $('<div/>', { class: "modal-content" });

    if(typeof config.id !== "undefined" && config.id){ modalOuter.prop("id", config.id); }
    if(typeof config.setSize !== "undefined" && config.setSize){ modalDialog.addClass(config.setSize); }
    if(typeof config.setDialogId !== "undefined" && config.setDialogId){ modalDialog.prop("id", config.setDialogId); }
    if(typeof config.setContentId !== "undefined" && config.setContentId){ modalContent.prop("id", config.setContentId); }

    var currentId = modalOuter.attr("id");
    var modalContentId = config.setContentId;

    var modalExist = $("#"+currentId);
    if(typeof modalOuter !== "undefined" && modalExist.length == 0){
        modalContent.appendTo(modalDialog);
        modalDialog.appendTo(modalOuter);
        modalOuter.appendTo("body");

        if(typeof config.setContent !== "undefined"){
            var setContentData = false;
            if(typeof config.setContent == "object"){
                var objectKey = Object.keys(config.setContent);
                var currentObjectKey = objectKey[0];
                /*** setContent - ajax ***/
                if(typeof currentObjectKey !== "undefined" && currentObjectKey == "ajax"){
                    var tempAjax = config.setContent.ajax;
                    var tempResponse = $.ajax(tempAjax);
                    tempResponse.done(function(json){
                        if(json.response){
                            modalHtmlContent = json.html;
                            modalContentObject = $(modalHtmlContent);
                            modalContentObject.appendTo(modalContent);
                            initDrawCallback(responseData);
                        }else{
                            toastr.error("Rendering of ajax modal content failed!", "Modal Content - Failed", 5000);
                        }
                    });
                }
                /*** setContent - parameters ***/
                if(typeof currentObjectKey !== "undefined" && currentObjectKey == "params"){
                    var parameter = config.setContent.params;
                    if(typeof parameter.title !== "undefined"){
                        var hasModalClose = (typeof parameter.close_header !== "undefined" && parameter.close_header == false)? false: true;
                        var subTitle = (typeof parameter.sub_title !== "undefined" && parameter.sub_title)? parameter.sub_title: null;
                        var currentModalTitle = setModalTitle(parameter.title, subTitle, hasModalClose);
                        currentModalTitle.appendTo(modalContent);
                    }
                    if(typeof parameter.content !== "undefined"){
                        var currentModalContent = setModalContent(parameter.content);
                        currentModalContent.appendTo(modalContent);
                    }
                    initDrawCallback(responseData);
                }
                if(typeof currentObjectKey !== "undefined" && currentObjectKey == "vue"){
                    var vueComponent = config.setContent.vue;
                    if(typeof vueComponent.components !== "undefined"){
                        var tempComponents = vueComponent.components;
                        if(typeof config.setContentId !== "undefined"){
                            var currentVueComponent = setModalComponent(tempComponents);
                            $.each(currentVueComponent, function(ii, vv){
                                vv.appendTo(modalContent);
                            });
                            initDrawCallback(responseData);
                        }else{
                            toastr.error("Failed to create vue component, set the modal content id first!", "Vue Component Failed", 5000);
                        }
                    }
                }
            }else{
                /*** setContent - plain text/html ***/
                modalContentObject = $(config.setContent);
                modalContentObject.appendTo(modalContent);
                initDrawCallback(responseData);
            }
        }
    }

    var initTempVueIntance = function(responseData){
        if(typeof responseData !== "undefined"){
            var tempVueIntance = config.initVueInstance(responseData);
            if(typeof tempVueIntance !== "undefined"){
                vueIntance = tempVueIntance;
            }
        }
    }

    var initDrawCallback = function(responseData){
        initTempVueIntance(initTempVueIntance(responseData));
        return config.drawCallback(responseData);
    }

    var responseData = {
        id: currentId,
        modal: modalOuter,
        modalContent: modalContent,
        contentId: modalContentId,
        modalShow: function(callback){
            modalOuter.modal("show");
            return (typeof callback == "function" && callback)? callback(): callback;
        },
        modalClose: function(callback){
            modalOuter.modal("hide");
            return (typeof callback == "function" && callback)? callback(): callback;
        },
        getContent: function(boolean){
            if(boolean == true){ return modalContentObject; }
            else{ return modalHtmlContent; }
        },getVueInstance: function(){
            return vueIntance;
        }
    };

    return responseData;
}