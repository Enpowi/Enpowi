Vue.directive('pager', {
  update: function (value) {
    value.pages = value.pages || 0;

    if (value.pages <= 1) return;

    var page = value.page || 1,
      pages = value.pages,
      size = value.size || 5,
      url = value.url,
      i = Math.floor(Math.max(1, page - size)),
      max = Math.ceil(Math.min(pages, page + size)),
      pageBeforeAnchor = '',
      pageAfterAnchor = '',
      pageAnchors = '',
      el = this.el;

    if (el.nodeName !== 'NAV') {
      console.log('Warning: pager should be used with nav elements');
    }

    for (; i <= max; i++) {
      pageAnchors += '<li' + (page === i ? ' class="active"' : '') + '><a href="' + url + i + '">' + i + '</a></li>';
    }

    if (page > 1) {
      pageBeforeAnchor = '<li>\
                                <a href="' + url + (page - 1) + '" aria-label="Previous">\
                                    <span aria-hidden="true">&laquo;</span>\
                                </a>\
                            </li>';
    } else {
      pageBeforeAnchor = '<li>\
                                <a aria-label="Previous">\
                                    <span aria-hidden="true">&laquo;</span>\
                                </a>\
                            </li>';
    }

    if (page < pages) {
      pageAfterAnchor = '<li>\
                                <a href="' + url + (page + 1) + '" aria-label="Next">\
                                    <span aria-hidden="true">&raquo;</span>\
                                </a>\
                            </li>';
    } else {
      pageAfterAnchor = '<li>\
                                <a aria-label="Next">\
                                    <span aria-hidden="true">&raquo;</span>\
                                </a>\
                            </li>';
    }

    el.innerHTML = '<ul class="pagination">\
                            ' + pageBeforeAnchor + pageAnchors + pageAfterAnchor + '\
                        </ul>';
  }
});