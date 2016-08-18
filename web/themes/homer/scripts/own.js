jQuery(function () {
    (function ($, window, document, undefined) {
        $('.summernote1').summernote();


        $('.label-menu-corner').click(function (e) {
            $('.dropdown-menu1').toggle();
        });

        $(".datapicker").datepicker({autoclose: true});
        $(".datepicker").datepicker({autoclose: true});

        $("#appraisal-form").validate();
        $("#goose-form").validate();
        $(".form-validate").validate();

        $(".goose-form-comment1").validate();
        $(".goose-form-comment2").validate();
        $(".goose-form-comment3").validate();
        $('body').delegate('.bnt-delete', 'click', function (event) {
            var url = $(this).data('href');
            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = url;
                    } else {
                        console.log('cancel');
                    }
                });

        });
        $('body').delegate('.bnt-action', 'click', function (event) {
            var url = $(this).data('href');
            swal({
                    title: "Are you sure?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, do it!"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = url;
                    } else {
                        console.log('cancel');
                    }
                });

        });
        $(".suggest").autocomplete({
            source: $('#content').attr('data-url-subject'),
            select: function (event, ui) {
                var textSearch = $(this);
                var nameEntity = $('#content').attr('data-name-entity');
                var nameField = textSearch.attr('data-name-field');
                var html = ['<div class="form-group">',
                    '<input type="hidden" class="' + nameField + '" name="' + nameEntity + '[' + nameField + '][]" value="', ui.item.value, '"/>',
                    '<div class="col-sm-2"></div>',
                    '<div class="col-sm-4"><label for="">', ui.item.label, '</label></div>',
                    '<div class="col-sm-4"><label for="">', ui.item.department, '</label></div>',
                    '<div class="col-sm-2"><input type="button"  class="btn btn-default btn-remove" value="Remove"></div>',
                    '</div>'];
                var contributor = html.join('');
                if ($('.' + nameField).length) {
                    var added = false;
                    $('.' + nameField).each(function () {
                        if ($(this).val() == ui.item.value) {
                            added = true;
                        }
                    });
                    if (added == false) {
                        $(contributor).insertAfter(textSearch.parents('.form-group').next('.form-group'));
                        textSearch.val('');
                    } else {
                        textSearch.val('');
                    }
                } else {
                    $(contributor).insertAfter(textSearch.parents('.form-group').next('.form-group'));
                    textSearch.val('');
                }
                return false;
            }
        });
        $('body').delegate('.btn-remove', 'click', function (e) {
            $(this).parents('.form-group').remove();
        });
        //tab in appraisal
        var tab = window.location.hash;
        if (tab != '') {
            $(tab).addClass('active');
        }

        //tab in draft
        if ($('.draft').length) {
            var tabActive = $('.nav-tabs').find('li').first();
            tabActive.addClass('active');
            var tabContent = $('.tab-content').find('.tab-pane').first();
            tabContent.addClass('active');
        }
        //get link to create department goal in page view objective
        if ($('.get-department').length) {
            var baseUrl = $('.get-url-create-department-goal').data('href');
            var id = 0;
            $('.get-department').change(function () {
                id = $(this).val();
                if (id != 0) {
                    $(this).removeClass('error');
                } else {
                    $(this).addClass('error');
                }
            });
            $('.get-url-create-department-goal').click(function () {
                if (id != 0) {
                    $('.get-department').removeClass('error');
                    var url = baseUrl.replace('department-id', id);
                    window.location.href = url;
                } else {
                    $('.get-department').addClass('error');
                }
            });
        }

        //list appraisal
        if ($('tr.data-link').length) {
            $(".data-link-sub").click(function () {
                var tr = $(this).parent('tr');
                var link = tr.data('link');
                window.location.href = link;
            });
        }

    })(jQuery, window, document);


    /**
     *  @name  init menu
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "nestable";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                $('li.dd-item button').click(function () {
                    var children = $(this).parent('li').children('ol');
                    var action = $(this).data('action');
                    if (action == 'collapse') {
                        $(this).hide();
                        $(this).next().show();
                        children.hide();
                    } else {
                        $(this).hide();
                        $(this).prev().show();
                        children.show();
                    }
                });
                that.onChange();
                that.onLoad();

            },
            onChange: function () {
                $('select.filter-by').change(function () {
                    var option1Obj = $(this).data('filter-type1');
                    var option2Obj = $(this).data('filter-type2');
                    var html = '<option value="">Select type of filter</option>';
                    var type = $(this).val();
                    if (type == 1) {
                        for (i = 0; i < option1Obj.length; i++) {
                            html += '<option value="' + option1Obj[i] + '">' + option1Obj[i] + '</option>';
                        }
                    }
                    if (type == 2) {
                        for (i = 0; i < option2Obj.length; i++) {
                            html += '<option value="' + option2Obj[i] + '">' + option2Obj[i] + '</option>';
                        }
                    }
                    $('select.filter-type').html(html);
                });
            },
            onLoad: function () {
                var option1Obj = $('select.filter-by').data('filter-type1');
                var option2Obj = $('select.filter-by').data('filter-type2');
                var html = '<option value="">Select type of filter</option>';
                var type = $('select.filter-by').val();
                var selectedType = $('select.filter-type').data('selected')
                if (type == 1) {
                    for (i = 0; i < option1Obj.length; i++) {

                        var selected = (selectedType == option1Obj[i] ? 'selected' : '');
                        html += '<option ' + selected + ' value="' + option1Obj[i] + '">' + option1Obj[i] + '</option>';
                    }
                }
                if (type == 2) {
                    for (i = 0; i < option2Obj.length; i++) {
                        var selected = (selectedType == option2Obj[i] ? 'selected' : '');
                        html += '<option ' + selected + ' value="' + option2Obj[i] + '">' + option2Obj[i] + '</option>';
                    }
                }
                $('select.filter-type').html(html);
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);
    /**
     *  @name  init menu
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "nav";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.setActive();
            },
            setActive: function () {
                var urlrequest = $('#side-menu').data('request');
                $('#side-menu li a').each(function () {
                    if ($(this).attr('href') == urlrequest) {
                        $(this).parent().addClass('active');
                        $(this).parents('ul.nav-second-level').attr('aria-expanded', true).addClass('in');
                        $(this).parents('li').addClass('active');
                    }
                });
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "load-objective";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.onchangeGoal();
            },
            loadObjective: function (selector) {
                var that = this;
                var url = $(selector).data('href');
                var type = $(selector).data('type');
                var objectiveId = $(selector).data('objective');
                var id = $(selector).val();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id: id,
                        type: type,
                        objectiveId: objectiveId
                    },
                    success: function (result) {
                        if (result.status) {
                            $('.get-objective').html(result.html);
                        } else {
                        }
                    }
                });
            },
            onchangeGoal: function () {
                var that = this;
                that.onloadGoal();
                $('.load-objective').change(function () {
                    that.loadObjective(this)
                });
            },
            onloadGoal: function () {
                var that = this;
                var selector = $('.load-objective');
                that.loadObjective(selector);
            },

        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "comment";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.reply();
                that.loadmore();
                that.progressOnchange();
                that.progressOnCheckin();
                that.activeTab();
                that.edit();
            },
            activeTab: function () {
                $('#wizardControl a').click(function () {
                    $('#wizardControl a').removeClass('btn-primary').addClass('btn-default');
                    $(this).addClass('btn-primary').removeClass('btn-default');
                });
                $('#wizardControl1 a').click(function () {
                    $('#wizardControl1 a').removeClass('btn-primary').addClass('btn-default');
                    $(this).addClass('btn-primary').removeClass('btn-default');
                });
            },
            edit: function () {
                $('body').delegate('.comment-edit', 'click', function (e) {
                    $('.summernote1').summernote();
                    var messageContent = $(this).parents('.message').find('.message-wrap-content');
                    var formEdit = $(this).parents('.message').find('form');
                    messageContent.addClass('display-none');
                    formEdit.removeClass('display-none');
                });
                $('body').delegate('.comment-edit-close', 'click', function (e) {
                    var messageContent = $(this).parents('.message').find('.message-wrap-content');
                    var formEdit = $(this).parents('.message').find('form');
                    messageContent.removeClass('display-none');
                    formEdit.addClass('display-none');
                });


                $('body').delegate('.comment-edit-submit', 'click', function (e) {
                    var form = $(this).parents('form');
                    var messageContent = $(this).parents('.message').find('.message-wrap-content');
                    var url = form.attr('action');
                    var editor = form.find('.summernote1');
                    var sHTML = editor.code();
                    var messageContentText = $(this).parents('.message').find('.message-content-text');
                    form.find('textarea').html(sHTML);
                    form.submit(function (event) {
                        event.preventDefault();
                    });

                    $.post(url, form.serialize()).done(function (data) {
                        if (data.status) {
                            messageContentText.html(sHTML);
                            messageContent.removeClass('display-none');
                            form.addClass('display-none');
                        }
                    });
                    return false;

                });
            },
            reply: function () {
                $('body').delegate('.comment-reply', 'click', function (e) {
                    e.preventDefault();
                    $('.input-comment-text').parents('.chat-message').addClass('display-none');
                    $(this).parents('.box-comment').find('.input-comment-text').parents('.chat-message').removeClass('display-none');
                    $(this).parents('.box-comment').find('.input-comment-text').focus();
                });

                var url = $('.sub-comment-form').attr('action');
                $('body').delegate('.comment-reply-submit', 'click', function (e) {
                    var form = $(this).parents('.sub-comment-form');
                    var boxComment = $(this).parents('.chat-message');
                    var editor = form.find('.summernote1');
                    var sHTML = editor.code();
                    form.find('textarea').html(sHTML);
                    form.submit(function (event) {
                        event.preventDefault();
                    });
                    $.post(url, form.serialize()).done(function (data) {
                        var imgSrc = "/themes/homer/images/user-default.png";
                        if (data.url != '') {
                            imgSrc = data.url;
                        }
                        var htmlArray = [
                            '<div class="chat-message">',
                            '<img class="message-avatar" src="', imgSrc, '" />',
                            '<div class="message">',
                            '<div class="message-wrap-content">',
                            '<a class="message-author" href="#"> ', data.name, ' </a>',
                            '<span class="message-date">', data.date, '</span>',
                            '<span class="message-content message-content-text">', data.comment, '</span>',
                            '<div class="m-t-md">',
                            '<a class="btn btn-xs btn-default comment-reply"><i class="fa fa-mail-reply"></i></a>',
                            '<a class="btn btn-xs btn-default comment-edit"><i class="fa fa-edit"></i></a>',
                            '<a href="', data.urlDelete, '?url=', data.urlPageComment, '" class="btn btn-xs btn-default pull-right"><i class="fa fa-trash"></i></a>',
                            '</div>',
                            '</div>',


                            '<form action="', data.urlEdit, '" method="post" class="form-horizontal message-wrap-form-edit display-none" novalidate="novalidate">',
                            '<input type="hidden" name="comment[parent]" class="input-comment-parent form-control" value="92">',
                            '<div class="form-group">',
                            '<div class="col-sm-12">',
                            '<textarea name="comment[text]" class="form-control summernote1">', data.comment, '</textarea>',
                            '</div>',
                            '</div>',
                            '<div class="form-group">',
                            '<div class="col-sm-12 pull-right">',
                            '<input type="button" name="action[]" class="btn btn-danger2 comment-edit-close" value="Close">',
                            '<input type="button" name="action[]" class="btn btn-primary comment-edit-submit" value="Submit">',
                            '</div>',
                            '</div>',
                            '</form>',

                            '</div>',
                            '</div>'
                        ];
                        var htmlString = htmlArray.join('');
                        $(htmlString).insertBefore(boxComment);
                        editor.code('');
                    });
                    return false;

                });
                $('body').delegate('.comment-reply-close', 'click', function (e) {
                    $(this).parents('.chat-message').addClass('display-none');
                });
            },
            loadmore: function () {
                // var loading = $('button.view-more').ladda();
                $('.comment button.view-more').click(function () {
                    // loading.ladda('start');
                    var that = $(this);
                    var wrap = $(this).parents('.chat-message');
                    var url = $(this).data('href');
                    var offset = $(this).attr('data-offset');
                    var type = $(this).data('type');
                    var objectId = $(this).data('object-id');
                    var urlPageComment = $(this).data('url-page-comment')
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            offset: offset,
                            type: type,
                            objectId: objectId,
                            urlPageComment: urlPageComment,
                        },
                        success: function (result) {
                            // loading.ladda('stop');
                            if (result.hasComment) {
                                var html = result.html;
                                $(html).insertBefore(wrap);
                                $(that).attr('data-offset', result.offset);
                            } else {
                                wrap.remove();
                            }
                        }
                    });
                });
            },
            progressOnchange: function () {
                var that = this;
                that.slider(0);
                $('#comment_krs').change(function () {
                    var url = $(this).data('href');
                    var KRsId = $(this).val();
                    that.loadProgress(url, KRsId);
                });
            },
            loadProgress: function (url, KRsId) {
                var that = this;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id: KRsId,
                    },
                    success: function (result) {
                        if (result.status) {
                            that.slider(result.progress);
                        } else {
                            that.slider(0);
                        }
                    }
                });
            },
            slider: function (value) {
                $("#slider-range-min").slider({
                    range: "min",
                    value: value,
                    min: 0,
                    max: 100,
                    slide: function (event, ui) {
                        $("#amount").val(ui.value + '%');
                        $("#comment_progress").val(ui.value);
                    }
                });
                $("#comment_progress").val(value);
                $("#amount").val($("#slider-range-min").slider("value") + '%');
            },
            progressOnCheckin: function () {
                var that = this;
                $('.btn-check-in').click(function () {
                    var url = $(this).data('href');
                    var KRsId = $(this).data('id');
                    $(".comment_kr").val(KRsId);
                    that.loadProgressCheckin(url, KRsId);
                });
            },
            loadProgressCheckin: function (url, KRsId) {
                var that = this;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id: KRsId,
                    },
                    success: function (result) {
                        if (result.status) {
                            that.sliderCheckin(result.progress);
                        } else {
                            that.sliderCheckin(0);
                        }
                    }
                });
            },
            sliderCheckin: function (value) {
                $("#slider-range-min-popup").slider({
                    range: "min",
                    value: value,
                    min: 0,
                    max: 100,
                    slide: function (event, ui) {
                        $("#amount_popup").val(ui.value + '%');
                        $("#comment_progress_popup").val(ui.value);
                    }
                });

                $("#comment_progress_popup").val(value);
                $("#amount_popup").val($("#slider-range-min-popup").slider("value") + '%');
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "notifications";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.loadmore();
                that.viewmore();
            },
            loadmore: function () {
                // var loading = $('.comment .view-more').ladda();
                $('.notifications .view-more').off('click.fuck').on('click.fuck', function () {
                    // loading.ladda('start');
                    var that = $(this);
                    var wrapButtonLoadMore = $(this).parent('div');
                    var url = $(this).data('href');
                    var offset = $(this).attr('data-offset');
                    var type = $(this).data('type');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            offset: offset,
                            type: type,
                        },
                        success: function (result) {
                            // loading.ladda('stop');
                            if (result.hasNotification) {
                                var html = result.html;
                                $(html).insertBefore(wrapButtonLoadMore);
                                $(that).attr('data-offset', result.offset);
                            } else {
                                wrapButtonLoadMore.remove();
                            }
                        }
                    });
                });
            },
            viewmore: function () {
                $('body').delegate('.view-more-info', 'click', function (e) {
                    e.preventDefault();
                    var that = $(this);
                    var url = $(this).data('href');
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function (result) {
                            if (result.status) {
                                var html = result.html;
                                $(html).insertBefore(that);
                                that.parents('.vertical-timeline-content').removeClass('notification-not-view');
                                that.remove();
                                $('.number-notification-not-view').html(result.numberNotificationNotView);
                            }
                        }
                    });
                });
            }

        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "load-contributors";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.loadmore();
            },
            loadmore: function () {
                $('.contributors .view-more').click(function () {
                    var that = this;
                    var wrapButtonLoadMore = $(this).parents('div.wrap-button-loadMore');
                    var url = $(this).data('href');
                    var offset = $(this).data('offset');
                    var type = $(this).data('type');
                    var objectId = $(this).data('object-id');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            offset: offset,
                            type: type,
                            objectId: objectId,
                        },
                        success: function (result) {
                            if (result.hasRecord) {
                                var html = result.html;
                                $(html).insertBefore(wrapButtonLoadMore);
                                $(that).data('offset', result.offset);
                            } else {
                                wrapButtonLoadMore.remove();
                            }
                        }
                    });

                });
            }
        };

        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);
    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "load-reviewers";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.loadmore();
            },
            loadmore: function () {
                $('.reviewers .view-more').click(function () {
                    var that = this;
                    var wrapButtonLoadMore = $(this).parents('div.wrap-button-loadMore');
                    var url = $(this).data('href');
                    var offset = $(this).data('offset');
                    var type = $(this).data('type');
                    var objectId = $(this).data('object-id');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            offset: offset,
                            type: type,
                            objectId: objectId,
                        },
                        success: function (result) {
                            if (result.hasRecord) {
                                var html = result.html;
                                $(html).insertBefore(wrapButtonLoadMore);
                                $(that).data('offset', result.offset);
                            } else {
                                wrapButtonLoadMore.remove();
                            }
                        }
                    });

                });

            }
        };

        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "appraisal-create";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.table;
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.getTable();
                that.saveData();
            },
            getTable: function () {
                var width = $('.width-appraisal-create').width();
                var data = [
                    ["", "", "", "", "", "", ""],
                ];
                var container = document.getElementById('dataTable');
                var content = $('#content');
                var url = content.data('url-suggest');
                var reviewType = content.data('review-type');
                var cycle = content.data('cycle');
                var template = content.data('template');
                this.table = new Handsontable(container, {
                    data: data,
                    width: width,
                    height: 200,
                    minSpareRows: 1,
                    rowHeaders: false,
                    contextMenu: true,
                    colHeaders: [
                        "Employee Name",
                        "Reviewing Officer",
                        "Approving Officer",
                        "Review Type",
                        "Select Review Cycle Period",
                        "From",
                        "To",
                        "Appraisal Template",
                    ],
                    columns: [
                        {
                            type: 'autocomplete',
                            source: function (query, process) {
                                $.ajax({
                                    url: url,
                                    dataType: 'json',
                                    data: {
                                        query: query,
                                        type: 'EM'
                                    },
                                    success: function (response) {
                                        process(response.data);
                                    }
                                });
                            },
                            strict: true
                        },
                        {
                            type: 'autocomplete',
                            source: function (query, process) {
                                $.ajax({
                                    url: url,
                                    dataType: 'json',
                                    data: {
                                        query: query,
                                        type: 'RO'
                                    },
                                    success: function (response) {
                                        process(response.data);
                                    }
                                });
                            },
                            strict: true
                        },
                        {
                            type: 'autocomplete',
                            source: function (query, process) {
                                $.ajax({
                                    url: url,
                                    dataType: 'json',
                                    data: {
                                        query: query,
                                        type: 'AO'
                                    },
                                    success: function (response) {
                                        process(response.data);
                                    }
                                });
                            },
                            strict: true
                        },
                        {
                            type: 'dropdown',
                            source: reviewType
                        },
                        {
                            type: 'dropdown',
                            source: cycle
                        },
                        {
                            type: 'date',
                            dateFormat: 'DD-MM-YYYY',
                            correctFormat: true,
                        },
                        {
                            type: 'date',
                            dateFormat: 'DD-MM-YYYY',
                            correctFormat: true,
                        },
                        {
                            type: 'dropdown',
                            source: template
                        }
                    ]
                });

            },
            saveData: function () {
                var that = this;
                var save = document.getElementById('save');
                var url = $('#content').data('url-save');
                var redirectUrl = $('#content').data('url-redirect');
                Handsontable.Dom.addEvent(save, 'click', function () {
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        method: 'post',
                        data: {'data': JSON.stringify(that.table.getData())},
                        success: function (response) {
                            if (response.status) {
                                window.location.href = redirectUrl;
                            } else {
                                $('.error').show().html(response.message);
                            }
                        }
                    });
                });
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);


    /**
     *  @name
     *  @description
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "appraisal-workflow-performance-target-add";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.save();
                that.approve();
                that.reject();
                that.edit();
                that.delete();
                $('.add-button input').on('click', function () {
                    if ($('.btn-submit-new-target').length) {
                        $('.btn-submit-new-target').removeClass('display-none');
                    }
                    that.add();
                });
            },
            add: function () {
                var num = $('.item-performance-target').length + 1;

                var htmlArray = [
                    '<div class="row item-performance-target" data-id="">',
                    '<div class="table-responsive col-md-6">',
                    '<table class="table table-bordered table-striped" cellpadding="1" cellspacing="1">',
                    '<thead>',
                    '<tr>',
                    '<th class="text-center">No.</th>',
                    '<th class="text-center">Performance Target</th>',
                    '</tr>',
                    '</thead>',
                    '<tbody>',
                    '<tr>',
                    '<td colspan="2" class="text-left">Status : <span class="status-performance-target">N/A</span></td>',
                    '</tr>',
                    '<tr>',
                    '<td class="text-center">', num, '</td>',
                    '<td class="text-right">',
                    '<div class="form-group">',
                    '<textarea placeholder="What is on your mind?" class="form-control text-performance-target" rows="2"></textarea>',
                    '</div>',
                    '<div class="form-group">',
                    '<input type="button" class="btn btn-sm btn-default submit-performance-target" value="Save"/>',
                    '</div>',
                    '</td>',
                    '</tr>',
                    '</tbody>',
                    '</table>',
                    '</div>',
                    '</div>',
                ];
                var htmlString = htmlArray.join('');
                $(htmlString).insertBefore('.add-button');
            },
            save: function () {
                $("body").delegate(".submit-performance-target", "click", function () {
                    var submitButton = $(this);
                    var textArea = submitButton.parents('.text-right').find('textarea');
                    var performanceTarget = textArea.val();
                    if (performanceTarget == '') {
                        textArea.addClass('form-error');
                    } else {
                        var url = $("[data-appraisal-workflow-performance-target-add]").data('href-save');
                        var content = submitButton.parents('.item-performance-target');
                        var status = content.find('.status-performance-target');
                        var id = content.attr('data-id');
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: {performanceTarget: performanceTarget, id: id},
                            success: function (result) {
                                if (result.status) {
                                    content.attr('data-id', result.id);
                                    submitButton.parent('.form-group').remove();
                                    textArea.attr('readonly', true);
                                    textArea.attr('title', 'Click to edit.');
                                    textArea.addClass('text-performance-target-own');
                                    status.html(result.statusText);
                                }
                            }
                        });
                    }

                });
                $("body").delegate(".text-performance-target", "click", function () {
                    $(this).removeClass('form-error');
                });
            },
            edit: function () {
                $("body").delegate(".text-performance-target-own", "click", function () {
                    var htmlButtonEdit = '<div class="form-group"><input type="button" class="btn btn-sm btn-default delete-performance-target" value="Delete"><input type="button" class="btn btn-sm btn-default submit-performance-target" value="Save changes"></div>';
                    $(this).attr('readonly', false);
                    $(this).attr('title', '');
                    $(this).parents('.text-right').append(htmlButtonEdit);
                    $(this).removeClass('text-performance-target-own');
                });
            },
            approve: function () {
                $("body").delegate(".approve-performance-target", "click", function () {
                    var approveButton = $(this);
                    var textArea = approveButton.parents('.text-right').find('textarea');
                    var content = approveButton.parents('.item-performance-target');
                    var url = $("[data-appraisal-workflow-performance-target-add]").data('href-approve');
                    var status = content.find('.status-performance-target');
                    var id = content.attr('data-id');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {id: id},
                        success: function (result) {
                            if (result.status) {
                                textArea.attr('readonly', true);
                                approveButton.parent('.form-group').remove();
                                status.html(result.statusText);
                            }
                        }
                    });

                });
            },
            reject: function () {
                $("body").delegate(".reject-performance-target", "click", function () {
                    var rejectButton = $(this);
                    var textArea = rejectButton.parents('.text-right').find('textarea');
                    var content = rejectButton.parents('.item-performance-target');
                    var url = $("[data-appraisal-workflow-performance-target-add]").data('href-reject');
                    var status = content.find('.status-performance-target');
                    var id = content.attr('data-id');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {id: id},
                        success: function (result) {
                            if (result.status) {
                                textArea.attr('readonly', true);
                                rejectButton.parent('.form-group').remove();
                                status.html(result.statusText);
                            }
                        }
                    });

                });
            },
            delete: function () {
                $("body").delegate(".delete-performance-target", "click", function () {
                    var rejectButton = $(this);
                    var content = rejectButton.parents('.item-performance-target');
                    var url = $("[data-appraisal-workflow-performance-target-add]").data('href-delete');
                    var id = content.attr('data-id');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {id: id},
                        success: function (result) {
                            if (result.status) {
                                content.remove();
                            }
                        }
                    });

                });
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);


    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "appraisal-comment";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                that.loadmore();
                that.submitFormAjax();

            },
            loadmore: function () {
                $('.appraisal-comment .view-more').click(function () {
                    var that = $(this);
                    var buttonViewMore = $(this).parent('div');
                    var url = $(this).data('href');
                    var offset = $(this).attr('data-offset');
                    var type = $(this).data('comment-type');
                    var allowEdit = $(this).data('allow-edit');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            offset: offset,
                            type: type,
                            allowEdit: allowEdit
                        },
                        success: function (result) {
                            if (result.hasComment) {
                                var html = result.html;
                                $(html).insertBefore(buttonViewMore);
                                $(that).attr('data-offset', result.offset);
                            } else {
                                buttonViewMore.remove();
                                console.log(buttonViewMore);
                            }
                        }
                    });
                });
            },
            submitFormAjax: function () {
                $('body').delegate('.button-edit-comment', 'click', function (e) {
                    e.preventDefault();
                    var textComment = $(this).parent('p');
                    var form = $(this).parents('.message-content').find('form');
                    textComment.hide();
                    form.show();
                });

                $('body').delegate('.input-comment-text', 'keypress', function (e) {
                    var contentComment = $(this).parents('.message-content').find('p.content-comment');
                    var inputComment = $(this);
                    var form = $(this).parent('form');
                    var url = form.attr('action');
                    form.submit(function (event) {
                        event.preventDefault();
                    });
                    var key = e.which;
                    if (key == 13 && inputComment.val() != '') {
                        $.post(url, form.serialize()).done(function (data) {
                            if (data.status) {
                                contentComment.show().html(data.comment);
                                form.hide();
                            }
                        });
                        return false;
                    }
                });
            },
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "appraisal-overall";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                if ($('#appraisal_overall_review_finalScore').length) {
                    $('#appraisal_overall_review_finalScore').change(function () {
                        var value = $(this).find("option:selected").text();
                        if (value != 'Select') {
                            $('#appraisal_overall_review_finalScoreText').val(value);
                        } else {
                            $('#appraisal_overall_review_finalScoreText').val('');
                        }
                    });
                }

            },
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "appraisal-recommendation";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                if ($('.recommendation-reviewer').length) {
                    $('.checkbox-control').click(function () {
                        console.log(1);
                        var row = $(this).parents('.margin-bottom-10');
                        if ($(this).is(':checked')) {
                            row.find('.form-control').attr('disabled', false).addClass('control-highlight');
                        } else {
                            row.find('.form-control').attr('disabled', true).removeClass('control-highlight');
                        }
                    });
                    if (!$('.btn-reviewer-submit').length) {
                        $('#appraisal_nomination_qualify1,#appraisal_nomination_qualify2,#appraisal_nomination_qualify3,#appraisal_nomination_qualify4,#appraisal_nomination_qualify5,#appraisal_nomination_reasonReviewer').attr('disabled', true);
                    }
                }
                if ($('.recommendation-approver').length) {
                    if (!$('.btn-approver-submit').length) {
                        $('#appraisal_nomination_support_0').attr('disabled', true);
                        $('#appraisal_nomination_support_1').attr('disabled', true);
                        $('#appraisal_nomination_support_2').attr('disabled', true);
                        $('#appraisal_nomination_reasonApprover').attr('disabled', true);
                    }
                }

            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);


    /**
     *  @name  add department
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "add-department";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                $("body").delegate("input[type='text'],select", "blur", function () {
                    that.saveDepartment(this);
                });
                $('.add-button a').on('click', function () {
                    that.addDepartment();
                });
            },
            addDepartment: function () {
                var country = $('select').first().html();
                var htmlArray = ['<div class="form-group" data-id="">',
                    '<div class = "col-sm-4" >',
                    '<input type = "text" placeholder="Department Name" name = "" required = "required" class = "name form-control" >',
                    '</div>',
                    '<div class = "col-sm-2" >',
                    '<input type = "text" placeholder="Office Location" name = "" required = "required" class = "location form-control" >',
                    '</div>',
                    '<div class="col-sm-2">',
                    '<select class="country form-control m-b">',
                    country,
                    '</select>',
                    '</div>',
                    '</div>'];
                var htmlString = htmlArray.join('');
                $(htmlString).insertBefore('.add-button');
            },
            saveDepartment: function (prevElement) {
                var that = this;
                var name = $(prevElement).parents('.form-group').find('.name');
                var location = $(prevElement).parents('.form-group').find('.location');
                var country = $(prevElement).parents('.form-group').find('.country');
                var id = $(prevElement).parents('.form-group').attr('data-id');
                var url = $('#content').data('href');
                if (name.val() != '' && location.val() != '' && country.val() != '') {
                    that.callAjax(name.val(), location.val(), country.val(), id, prevElement);
                }
            },
            callAjax: function (name, location, country, id, PrevElement) {
                var url = $('#content').data('href');
                var saveSuccessfull = '<div class="col-sm-2 save-success text-center"><i class="fa fa-check"></i></div>';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {name: name, location: location, country: country, id: id},
                    success: function (result) {
                        if (result.status) {
                            if (!$(PrevElement).parents('.form-group').find('.save-success').length) {
                                $(PrevElement).parents('.form-group').append(saveSuccessfull);
                                $(PrevElement).parents('.form-group').attr('data-id', result.id);
                            }
                        }
                    }
                });
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);

    /**
     *  @name  add user
     *  @description list view
     *  @version 1.0
     *  @options
     *    option
     *  @events
     *    event
     *  @methods
     *    init
     */
    (function ($, window, document, undefined) {
        var pluginName = "create-users";

        // The actual plugin constructor
        function Plugin(element, options) {
            this.element = element;
            this.options = $.extend({}, $.fn[pluginName].defaults, options);
            this.init();
        }

        Plugin.prototype = {
            init: function () {
                var that = this;
                $("body").delegate(".submit-add-user", "click", function () {
                    that.saveUser(this);
                });
                $('.add-button a').on('click', function () {
                    that.addUser();
                });
            },
            addUser: function () {
                var department = $('select.department').first().html();
                var userType = $('select.user-type').first().html();
                var htmlArray = [
                    ' <form class="form-horizontal" method="post" enctype="multipart/form-data">',
                    '<div class="form-group form-group-add">',
                    '<input type="hidden" class="user-id" name="user[userId]" required="required" value="">',
                    '<div class = "col-sm-2" >',
                    '<input type="text" placeholder="First Name" name="user[firstName]" required="required" class="first-name form-control">',
                    '</div>',
                    '<div class = "col-sm-2" >',
                    '<input type="text" placeholder="Last Name" name="user[lastName]" required="required" class="last-name form-control">',
                    '</div>',
                    '<div class="col-sm-2">',
                    '<select class="department form-control" name="user[departments]">',
                    department,
                    '</select>',
                    '</div>',
                    '<div class = "col-sm-2" >',
                    '<input type="text" placeholder="Employee Class" name="user[employeeClass]" required="required" class="employee-class form-control">',
                    '</div>',
                    '<div class = "col-sm-2" >',
                    '<input type="text" placeholder="Email" name="user[email]" required="required" class="email form-control">',
                    '<p style="color:red"><span class="email-error hidden"></span></p>',
                    '</div>',
                    '<div class="col-sm-2">',
                    '<select class="user-type form-control" name="user[userType]">',
                    userType,
                    '</select>',
                    '</div>',
                    '</div>',
                    '<div class="form-group form-group-add">',
                    '<div class="col-sm-3">',
                    '<input type="file" name="user[media][binaryContent]">',
                    '</div>',
                    '<div class="col-sm-3">',
                    '<input type="button" class = "btn btn-sm btn-primary submit-add-user" name="Submit"  value="Submit">',
                    '</div>',
                    '</div>',
                    '</form>'
                ];
                var htmlString = htmlArray.join('');
                $(htmlString).insertBefore('.add-button');
            },
            saveUser: function (prevElement) {
                var that = this;
                var firstName = $(prevElement).parents('.form-group').find('.first-name').val();
                var lastName = $(prevElement).parents('.form-group').find('.last-name').val();
                var department = $(prevElement).parents('.form-group').find('.department').val();
                var employeeClass = $(prevElement).parents('.form-group').find('.employee-class').val();
                var email = $(prevElement).parents('.form-group').find('.email').val();
                var userType = $(prevElement).parents('.form-group').find('.user-type').val();
                var id = $(prevElement).parents('.form-group').attr('data-id');
                var url = $('#content').data('href');
                var form = $(prevElement).parents('.form-horizontal');
                if (email != '' && firstName != '') {
                    that.callAjax(form, prevElement);
                }
            },
            callAjax: function (form) {
                var saveSuccessfull = '<div class="col-sm-1 save-success text-center"><i class="fa fa-check"></i></div>';
                var url = $('#content').data('href');
                var formData = new FormData($(form)[0]);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    async: false,
                    success: function (result) {
                        if (result.status) {
                            if (!form.find('.save-success').length) {
                                form.find('.form-group-add').last().append(saveSuccessfull);
                                form.find('.email-error').addClass('hidden');
                                form.find('.user-id').val(result.id)
                            }
                        } else {
                            form.find('.email-error').removeClass('hidden').html(result.message);
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });

                return false;
            }
        };
        $.fn[pluginName] = function (options) {
            return this.each(function () {
                if (!$.data(this, pluginName)) {
                    $.data(this, pluginName,
                        new Plugin(this, options));
                }
            });
        };
        $.fn[pluginName].defaults = {
            propertyName: 1
        };
        $(function () {
            $('[data-' + pluginName + ']')[pluginName]();
        });

    })(jQuery, window, document);


});



