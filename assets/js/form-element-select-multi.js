$(document).ready(function () {
  $(document).on('click', '[data-select-multi] [data-select]', function () {
    const root = $(this).closest('[data-select-multi]');
    const elementName = root.data('select-multi');
    const sourceNamespace = root.data('select-source').split('\\');
    const template = root.find('script').html();
    const container = root.find('[data-select-multi-container]');
    const selectFields = JSON.parse(atob(root.data('select-fields')));
    selectFields['id'] = 'id';

    const iframeUrl = '/' + sourceNamespace[sourceNamespace.length - 1].toLowerCase() + '/select';

    const handler = (event) => {
      const item = JSON.parse(atob(event.data.data));

      let html = template;

      $.each(selectFields, (key, value) => {
        html = html.replaceAll('{{' + key + '}}', item[value]);
      });

      modal.hide();

      container.append(html);
      container.sortable({});
    };

    window.addEventListener('message', handler, false);

    modal.container('<iframe class="select-multi-iframe" src="' + iframeUrl + '"></iframe>', () => {
      window.removeEventListener('message', handler);
    });
  });

  $(document).on('click', '[data-select-multi] [data-select-multi-remove]', function () {
    modal.confirm('Действительно удалить?', () => {
      $(this).closest('[data-select-multi-item]').remove();
      $(this).closest('[data-select-multi-container]').sortable({});
    });
  });

});
