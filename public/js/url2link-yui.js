// updateURL2Link(el, attr) : update plain URLs to link {{{
var updateURL2Link = function (el, attr) {
  // preparation {{{
  el = YAHOO.util.Dom.get(el);
  var nodes = [];
  if (el.length) {
    while (a = el.shift()) {
      nodes.push(a);
    }
  }
  else {
    nodes.push(el);
  }
  attr = attr || {};
  attr.target = attr.target || null;
  attr.allowTags = attr.allowTags || [
    "abbr", "acronym", "address", "applet", "b", "bdo", "big", "blockquote", "body", 
    "caption", "center", "cite", "code", "dd", "del", "div", "dfn", "dt", "em", 
    "fieldset", "font", "form", "h1", "h2", "h3", "h4", "h5", "h6", "i", "iframe",
    "ins", "kdb", "li", "object", "pre", "p", "q", "samp", "small", "span", "strike", 
    "s", "strong", "sub", "sup", "td", "th", "tt", "u", "var"
  ];
  if (!YAHOO.lang.isArray(attr.allowTags)) {
    throw('The allowTags attribute must be an array.'); 
    return;
  }
  attr.urlRegexp = attr.urlRegexp || /(https?:\/\/[^\s+\"\<\>]+)/ig;
  if (!/\/(\\[^\x00-\x1f]|\[(\\[^\x00-\x1f]|[^\x00-\x1f\\\/])*\]|[^\x00-\x1f\\\/\[])+\/[gim]*/.test(attr.urlRegexp.toString())) {
    throw('The urlRegexp attribute is not a validate regular expression.'); 
    return;
  }
  // }}}
  // walkTheDOM(node, func) : Douglas Crockfors' DOM tarversing method {{{
  var walkTheDOM = function (node, func) {
    func(node);
    node = node.firstChild;
    while (node) {
      walkTheDOM(node, func);
      node = node.nextSibling;
    }
  }
  // }}}
  // getTextNode(root) : Get all text node based on walkTheDOM method {{{
  var getTextNode = function (root) {
    var results = [];
    walkTheDOM(root, function (node) {
      if (node.nodeType == 3) {
        results.push(node);
      }
    });
    return results;
  }
  // }}}
  // update(el) : Modify from Linkfy UserScript {{{
  var update = function (el) {
    var nodeEls = getTextNode(el);
    for (var i = nodeEls.length - 1; i >= 0; i--) {
      var nodeEl = nodeEls[i];
      // filter illegal parentNode  {{{
      var isAllow = false;
      for (var x in attr.allowTags) {
        if (nodeEl.parentNode.nodeName.toLowerCase() === attr.allowTags[x]) {
          isAllow = true;
          break;
        }
      }
      if (!isAllow) {
        nodeEls.splice(i, 1);
        continue;
      }
      // }}}
      // match the url pattern {{{
      var matches = nodeEl.nodeValue.match(attr.urlRegexp);
      if (!matches) {
        nodeEls.splice(i, 1);
      }
      // }}}
    }
    for (var i in nodeEls) {
      var nodeEl = nodeEls[i];
      var spanEl = document.createElement('span'); 
      var source = nodeEl.nodeValue; 
      nodeEl.parentNode.replaceChild(spanEl, nodeEl); 
      attr.urlRegexp.lastIndex = 0;
      for (var match = null, lastLastIndex = 0; (match = attr.urlRegexp.exec(source)); ) {
        var value = source.substring(lastLastIndex, match.index);
        var textEl = document.createTextNode(value);
        spanEl.appendChild(textEl);
        var linkEl = document.createElement('a');
        linkEl.setAttribute('href', match[0]);
        if (attr.target) {
            linkEl.setAttribute('target', attr.target);
        }
        linkEl.appendChild(document.createTextNode(match[0]));
        spanEl.appendChild(linkEl);
        lastLastIndex = attr.urlRegexp.lastIndex;
      }
      var value = source.substring(lastLastIndex);
      spanEl.appendChild(document.createTextNode(value));
      if (spanEl.normalize) {
        spanEl.normalize();
      }
    }
  }
  // }}}
  while (a = nodes.shift()) {
    update(a);
  }
}
// }}}