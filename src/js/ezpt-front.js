jQuery(document).ready(function($) {
    $('#folder-select').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            window.location.href = selectedValue;
        }
    });

    $('.pin-click').on('click', function(e) {
        e.preventDefault();
        var pin_id = $(this).attr('pin-id');
        $('#pin_id').val(pin_id);

        var content = $('#nefi').html();

        var ppp = $.dialog({
            theme: 'nefi',
            title: null,
            boxWidth: '940px',
            useBootstrap: false,
            content: function(){
                var self = this;
                self.setContent(content);
            },
            onContentReady: function(){
                this.setContentAppend(content);
                var ne = this;
                var swiper_neFi = new Swiper(".neFi", {
                    navigation: {
                      nextEl: ".swiper-button-next",
                      prevEl: ".swiper-button-prev",
                    },
                    on: {
                        slideChange: function () {
                            var pin_id = $('.swiper-slide').eq(this.activeIndex).attr('pin-id');
                            $('#pin_id').val(pin_id);
                        },
                        afterInit: function () {
                            this.activeIndex = $('.swiper-slide[pin-id='+pin_id+']').index();
                        }
                    }
                });

                var popup = this;

                this.$content.find('.open-folders').click(function(){
                    var content2 = $('#folders-list').html();
                    var pin_id = $(this).attr('pin-id');
                    var save = $.dialog({
                        theme: 'nefi-save',
                        title: null,
                        boxWidth: '396px',
                        useBootstrap: false,
                        content: function(){
                            var self = this;
                            self.setContent(content2);
                        },
                        onContentReady: function(){
                            this.setContentAppend(content2);
                            this.$content.find('.folder .item').click(function(e){
                                e.preventDefault();
    
                                var folder_id = $(this).attr('folder-id');
                                var folder_name = $(this).attr('folder-name');
                            
                                var data = {
                                    pin_id: pin_id,
                                    folder_id: folder_id
                                };
                                console.log(data);
                            
                                fetch(ezptFront.ajax_url + 'update-folder/', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-WP-Nonce': ezptFront.nonce
                                    },
                                    body: JSON.stringify(data)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    save.close();
                                    $.confirm({
                                        theme: 'nefi-save',
                                        title: null,
                                        boxWidth: '491px',
                                        useBootstrap: false,
                                        content: `
                                        <div style="text-align: center;">
                                            <h4>Conteúdo Salvo</h4>
                                            <p>Você salvou este conteúdo em ${folder_name}!</p>
                                        </div>
                                        `,
                                        buttons: {
                                            close: {
                                                text: 'Fechar',
                                                action: function () {}
                                            }
                                        }
                                    });
                                    var new_pilula = '<div class="pilula"><div class="tags">'+folder_name+'</div><button onClick="window.location.reload();" class="excluir peq" folder-id="'+folder_id+'" pin-id="'+pin_id+'">&times;</button></div>';
                                    if ( popup.$content.find('.meta .varios') ) {
                                        popup.$content.find('.meta .varios').append(new_pilula);
                                    } else {
                                        popup.$content.find('.meta').append('<div class="varios">' + new_pilula + '</div>');
                                    }
                                })
                                .catch((error) => {
                                    console.error('Erro:', error);
                                });
                            });
                            this.$content.find('.folder-create').click(function(e){
                                var content3 = $('#create-folder-wraper').html();
                                var create_folder = $.dialog({
                                    theme: 'nefi-save',
                                    title: null,
                                    boxWidth: '491px',
                                    useBootstrap: false,
                                    content: function(){
                                        var self = this;
                                        self.setContent(content3);
                                    },
                                    onContentReady: function(){
                                        this.setContentAppend(content3);
                                        var create = this;
                                        this.$content.find('#create-folder').click(function(e){
                                            e.preventDefault();
    
                                            var title = create.$content.find('#title').val();
                                        
                                            var data = {
                                                title: title
                                            };
                                            console.log(data);
                                        
                                            fetch(ezptFront.ajax_url + 'create-folder/', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-WP-Nonce': ezptFront.nonce
                                                },
                                                body: JSON.stringify(data)
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                var pin_id = $('#pin_id').val();
                                                var folder_id = data.folder_id;

                                                var data = {
                                                    pin_id: pin_id,
                                                    folder_id: folder_id
                                                };
                                            
                                                fetch(ezptFront.ajax_url + 'update-folder/', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-WP-Nonce': ezptFront.nonce
                                                    },
                                                    body: JSON.stringify(data)
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    console.log('Sucesso:', data);
                                                    create_folder.close();
                                                    save.close();
                                                    $.confirm({
                                                        theme: 'nefi-save',
                                                        title: null,
                                                        boxWidth: '491px',
                                                        useBootstrap: false,
                                                        content: `
                                                        <div style="text-align: center;">
                                                            <h4>Conteúdo Salvo</h4>
                                                            <p>Você salvou este conteúdo na pasta ${title}</p>
                                                        </div>
                                                        `,
                                                        buttons: {
                                                            close: {
                                                                text: 'Fechar',
                                                                action: function () {}
                                                            }
                                                        }
                                                    });
                                                })
                                                .catch((error) => {
                                                    console.error('Erro:', error);
                                                });
                                            })
                                            .catch((error) => {
                                                console.error('Erro:', error);
                                            });
                                        });
                                        this.$content.find('#cancel').click(function(e){
                                            create_folder.close();
                                        });
                                    }
                                });
                            });
                        }
                    });
                });
                this.$content.find('.excluir').click(function(){
                    var folder_id = $(this).attr('folder-id');
                    var exclude_pin = $.confirm({
                        theme: 'nefi-save',
                        title: 'Excluir',
                        boxWidth: '491px',
                        useBootstrap: false,
                        content: 'Tem certeza que deseja excluir este conteúdo?',
                        buttons: {
                            cancel: {
                                text: 'Voltar',
                                action: function () {}
                            },
                            ok: {
                                text: 'Sim, quero excluir',
                                action: function () {
                            
                                    var data = {
                                        pin_id: pin_id,
                                        folder_id: folder_id
                                    };
                                    console.log(data);
                                
                                    fetch(ezptFront.ajax_url + 'remove-pin-from-folder/', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-WP-Nonce': ezptFront.nonce
                                        },
                                        body: JSON.stringify(data)
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        $.confirm({
                                            theme: 'nefi-save',
                                            title: null,
                                            boxWidth: '491px',
                                            useBootstrap: false,
                                            content: `
                                            <div style="text-align: center;">
                                                <h4>Excluir</h4>
                                                <p>Conteúdo excluído com sucesso!</p>
                                            </div>
                                            `,
                                            buttons: {
                                                close: {
                                                    text: 'Voltar',
                                                    action: function () {}
                                                }
                                            }
                                        });
                                    })
                                    .catch((error) => {
                                        console.error('Erro:', error);
                                    });
                                }
                            }
                        }
                    });
                });
            }
        });
    });
});
