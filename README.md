# Easy PinThis

## English

### Description
**Easy PinThis** is a plugin that allows you to create and manage Pins and Pin Folders in WordPress. This plugin provides custom post types for Pins and Pin Folders, taxonomies for categorization, custom meta boxes for additional settings, REST API endpoints for managing Pin Folders, and shortcodes to display Pins and Pin Folders on the front end.

### Features
- Custom Post Type for Pins (`ez_pin`).
- Custom Post Type for Pin Folders (`ez_pin_folder`).
- Custom Meta Boxes for additional information on Pins and Pin Folders.
- Four custom taxonomies for categorizing Pins: `ez_pt_category`, `ez_pt_department`, `ez_pt_season`, `ez_pt_designer`.
- REST API routes for creating and updating Pin Folders.
- Shortcodes: `[my-pins]` to display user-created Pin Folders and `[list-pins]` to display Pins in a Pin Folder.

### Installation
1. Download the plugin and upload it to the `/wp-content/plugins/` directory, or install it directly from the WordPress plugins repository.
2. Activate the plugin through the "Plugins" menu in WordPress.

### Usage
- **Creating Pins:** Go to "Pins" in the WordPress dashboard and create new pins with titles, summaries, and featured images.
- **Managing Pin Folders:** Go to "Pin Folders" in the WordPress dashboard to create and manage folders. Use the "Settings" metabox to select Pins to include in a folder.
- **REST API:** Use the `/wp-json/easy-pinthis/v1/create-folder/` endpoint to create a Pin Folder and `/wp-json/easy-pinthis/v1/update-folder/` to update a folder with new Pins.
- **Shortcodes:** Use the `[my-pins]` shortcode to list all folders created by the logged-in user. Use the `[list-pins]` shortcode within a single Pin Folder view to list the Pins in that folder.

### Requirements
- WordPress 5.0 or higher.
- PHP 7.2 or higher.

### License
This plugin is licensed under the GPL-2.0-or-later license. See the `LICENSE` file for more details.

---

## Português

### Descrição
**Easy PinThis** é um plugin que permite criar e gerenciar Pins e Pastas de Pins no WordPress. Este plugin fornece tipos de post personalizados para Pins e Pastas de Pins, taxonomias para categorização, metaboxes personalizados para configurações adicionais, endpoints da REST API para gerenciar Pastas de Pins e shortcodes para exibir Pins e Pastas de Pins no frontend.

### Funcionalidades
- Tipo de post personalizado para Pins (`ez_pin`).
- Tipo de post personalizado para Pastas de Pins (`ez_pin_folder`).
- Metaboxes personalizados para informações adicionais em Pins e Pastas de Pins.
- Quatro taxonomias personalizadas para categorizar Pins: `ez_pt_category`, `ez_pt_department`, `ez_pt_season`, `ez_pt_designer`.
- Rotas da REST API para criar e atualizar Pastas de Pins.
- Shortcodes: `[my-pins]` para exibir Pastas de Pins criadas pelo usuário e `[list-pins]` para exibir Pins em uma Pasta de Pins.

### Instalação
1. Baixe o plugin e envie para o diretório `/wp-content/plugins/`, ou instale diretamente do repositório de plugins do WordPress.
2. Ative o plugin através do menu "Plugins" no WordPress.

### Uso
- **Criando Pins:** Vá para "Pins" no painel do WordPress e crie novos pins com títulos, resumos e imagens destacadas.
- **Gerenciando Pastas de Pins:** Vá para "Pastas de Pins" no painel do WordPress para criar e gerenciar pastas. Use o metabox "Configurações" para selecionar os Pins a serem incluídos na pasta.
- **REST API:** Use o endpoint `/wp-json/easy-pinthis/v1/create-folder/` para criar uma Pasta de Pins e `/wp-json/easy-pinthis/v1/update-folder/` para atualizar uma pasta com novos Pins.
- **Shortcodes:** Use o shortcode `[my-pins]` para listar todas as pastas criadas pelo usuário logado. Use o shortcode `[list-pins]` dentro de uma visualização única de Pasta de Pins para listar os Pins naquela pasta.

### Requisitos
- WordPress 5.0 ou superior.
- PHP 7.2 ou superior.

### Licença
Este plugin é licenciado sob a licença GPL-2.0-or-later. Veja o arquivo `LICENSE` para mais detalhes.
