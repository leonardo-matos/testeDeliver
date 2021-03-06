var appName;
var popupMask;
var popupDialog;
var clientId;
var realm;
var oauth2KeyName;
var redirect_uri;
var clientSecret;
var scopeSeparator;

function handleLogin() {
  var scopes = [];

  var auths = window.swaggerUi.api.authSchemes || window.swaggerUi.api.securityDefinitions,
      passwordFlow = false;

  if(auths) {
    var key;
    var defs = auths;
    for(key in defs) {
      var auth = defs[key];
      if(auth.type === 'oauth2') {
        passwordFlow = auth.flow === 'password';

        if (auth.scopes) {
          oauth2KeyName = key;
          var scope;

          if(Array.isArray(auth.scopes)) {
            // 1.2 support
            var i;
            for(i = 0; i < auth.scopes.length; i++) {
              scopes.push(auth.scopes[i]);
            }
          }
          else {
            // 2.0 support
            for(scope in auth.scopes) {
              scopes.push({scope: scope, description: auth.scopes[scope]});
            }
          }
        }
      }
    }
  }

  if(window.swaggerUi.api
    && window.swaggerUi.api.info) {
    appName = window.swaggerUi.api.info.title;
  }

  $('.api-popup-dialog').remove();

  popupDialog = ['<div class="api-popup-dialog">'];

  if (passwordFlow === true) {
	  
	  
    popupDialog = popupDialog.concat([
      '<div class="api-popup-title">Autenticação utilizando o fluxo "password" </div>',
      '<p><label>Usuário: </label> <input type="text" id="username" > ',
      '<label>Senha: </label> <input type="password" id="password" ></p>',
      '<br/>',
      '<p><label>client_id: </label> <input type="text" id="client_id" > ',
      '<label>client_secret: </label> <input type="password" id="client_secret" ></p>',
      '<script> document.getElementById("client_id").value = localStorage.getItem("client_id");',
      'document.getElementById("client_secret").value = localStorage.getItem("client_secret");</script>'
    ]);
    
    

    
  }

  popupDialog = $(popupDialog.concat([
        '<div class="api-popup-title">Selecione os escopos(scopes) OAuth2.0 </div>',
        '<div class="api-popup-content">',
          '<p>Escopos(Scopes) são utilizados em uma API para conceder diferentes niveis de acesso aos dados em nome do usuário final. Cada API pode declarar um ou mais escopos </br>',
            '<a target="_blank" href="https://tools.ietf.org/html/rfc6749#section-3.3">Aprenda como usar</a>',
          '</p> <br/>',
          '<p><strong>' + appName + '</strong>  requer os seguintes escopos. Selecione quais você deseja conceder para a Swagger UI.</p>',
          '<ul class="api-popup-scopes">',
          '</ul>',
          '<p class="error-msg"></p>',
          '<div class="api-popup-actions"><button class="api-popup-authbtn api-button green" type="button">Autenticar</button>  <button class="api-popup-cancel api-button gray" type="button">Cancelar</button></div>',
        '</div>',
        '</div>']).join(''));

  $(document.body).append(popupDialog);

  popup = popupDialog.find('ul.api-popup-scopes').empty();
  for (i = 0; i < scopes.length; i ++) {
    scope = scopes[i];
    str = '<li><input type="checkbox" id="scope_' + i + '" scope="' + scope.scope + '"/>' + '<label for="scope_' + i + '">' + scope.scope;
    if (scope.description) {
      str += '<br/><span class="api-scope-desc">' + scope.description + '</span>';
    }
    str += '</label></li>';
    popup.append(str);
  }

  var $win = $(window),
    dw = $win.width(),
    dh = $win.height(),
    st = $win.scrollTop(),
    dlgWd = popupDialog.outerWidth(),
    dlgHt = popupDialog.outerHeight(),
    top = (dh -dlgHt)/2 + st,
    left = (dw - dlgWd)/2;

  popupDialog.css({
    top: (top < 0? 0 : top) + 'px',
    left: (left < 0? 0 : left) + 'px'
  });

  popupDialog.find('button.api-popup-cancel').click(function() {
    popupMask.hide();
    popupDialog.hide();
    popupDialog.empty();
    popupDialog = [];
  });

  $('button.api-popup-authbtn').unbind();
  popupDialog.find('button.api-popup-authbtn').click(function() {
    popupMask.hide();
    popupDialog.hide();

    var authSchemes = window.swaggerUi.api.authSchemes;

    var host = window.location;
    var pathname = location.pathname.substring(0, location.pathname.lastIndexOf("/"));
    var defaultRedirectUrl = host.protocol + '//' + host.host + pathname + '/o2c.html';
    var redirectUrl = window.oAuthRedirectUrl || defaultRedirectUrl;
    var url = null;
    var passwordFlowDetails = null;

    for (var key in authSchemes) {
      if (authSchemes.hasOwnProperty(key)) {
        var flow = authSchemes[key].flow;

        if (authSchemes[key].type === 'oauth2' && flow) {
          var dets = authSchemes[key];
          window.swaggerUi.tokenName = dets.tokenName || 'access_token';

          if (flow === 'password') {
            passwordFlowDetails = dets;
            window.swaggerUi.tokenUrl = dets.tokenUrl;

          } else if (['implicit', 'accessCode'].indexOf(flow) !== -1) {

            url = dets.authorizationUrl + '?response_type=' + (flow === 'implicit' ? 'token' : 'code');
            window.swaggerUi.tokenUrl = (flow === 'accessCode' ? dets.tokenUrl : null);
          }
        }
        else if(authSchemes[key].grantTypes) {
          // 1.2 support
          var o = authSchemes[key].grantTypes;
          for(var t in o) {
            if(o.hasOwnProperty(t) && t === 'implicit') {
              var dets = o[t];
              var ep = dets.loginEndpoint.url;
              url = dets.loginEndpoint.url + '?response_type=token';
              window.swaggerUi.tokenName = dets.tokenName;
            }
            else if (o.hasOwnProperty(t) && t === 'accessCode') {
              var dets = o[t];
              var ep = dets.tokenRequestEndpoint.url;
              url = dets.tokenRequestEndpoint.url + '?response_type=code';
              window.swaggerUi.tokenName = dets.tokenName;
            }
          }
        }
      }
    }
    var scopes = []
    var o = $('.api-popup-scopes').find('input:checked');

    for(k =0; k < o.length; k++) {
      var scope = $(o[k]).attr('scope');

      if (scopes.indexOf(scope) === -1)
        scopes.push(scope);
    }

    if (passwordFlowDetails !== null) {
      handlePasswordFlow(passwordFlowDetails);

    } else {
      // Implicit auth recommends a state parameter.
      var state = Math.random ();

      window.enabledScopes=scopes;

      redirect_uri = redirectUrl;

      url += '&redirect_uri=' + encodeURIComponent(redirectUrl);
      url += '&realm=' + encodeURIComponent(realm);
      url += '&client_id=' + encodeURIComponent(clientId);
      url += '&scope=' + encodeURIComponent(scopes.join(scopeSeparator));
      url += '&state=' + encodeURIComponent(state);

      window.open(url);
    }
  });

  popupMask.show();
  popupDialog.show();
  return;
}

function handlePasswordFlow(auth) {
  var authParams = {
    grant_type: 'password',
    client_id: encodeURIComponent($('#client_id').val())|| encodeURIComponent(clientId),
    username: $('#username').val(),
    password: encodeURIComponent($('#password').val()),
    client_secret: encodeURIComponent($('#client_secret').val())|| encodeURIComponent(clientSecret)
  };
  
  localStorage.setItem("client_id", authParams.client_id);
  localStorage.setItem("client_secret", authParams.client_secret);

  
  $.ajax({
    url: auth.tokenUrl,
    type: 'post',
    data: authParams,
    success: function (data) {
      onOAuthComplete(data);
    },
    error: function(data) {
    retorno = JSON.parse(data.responseText);
      alert('Não foi possivel autenticar \n'+retorno.mensagem);
    }
  });
}

function handleLogout() {
  for(key in window.swaggerUi.api.clientAuthorizations.authz){
    window.swaggerUi.api.clientAuthorizations.remove(key)
  }
  window.enabledScopes = null;
  $('.api-ic.ic-on').addClass('ic-off');
  $('.api-ic.ic-on').removeClass('ic-on');

  // set the info box
  $('.api-ic.ic-warning').addClass('ic-error');
  $('.api-ic.ic-warning').removeClass('ic-warning');
}

function initOAuth(opts) {
  var o = (opts||{});
  var errors = [];

  appName = (o.appName||errors.push('missing appName'));
  popupMask = (o.popupMask||$('#api-common-mask'));
  popupDialog = (o.popupDialog||$('.api-popup-dialog'));
  clientId = (o.clientId||errors.push('missing client id'));
  clientSecret = (o.clientSecret||errors.push('missing client secret'));
  realm = (o.realm||errors.push('missing realm'));
  scopeSeparator = (o.scopeSeparator||' ');

  if(errors.length > 0){
    log('auth unable initialize oauth: ' + errors);
    return;
  }

  $('pre code').each(function(i, e) {hljs.highlightBlock(e)});
  $('.api-ic').unbind();
  $('.api-ic').click(function(s) {
    if($(s.target).hasClass('ic-off'))
      handleLogin();
    else {
      handleLogout();
    }
    false;
  });
}

window.processOAuthCode = function processOAuthCode(data) {
  var params = {
    'client_id': clientId,
    'client_secret': clientSecret,
    'code': data.code,
    'grant_type': 'authorization_code',
    'redirect_uri': redirect_uri
  }
  $.ajax(
  {
    url : window.swaggerUi.tokenUrl,
    type: "POST",
    data: params,
    success:function(data, textStatus, jqXHR)
    {
      onOAuthComplete(data);
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      onOAuthComplete("");
    }
  });
}

window.onOAuthComplete = function onOAuthComplete(token) {
  if(token) {
    if(token.error) {
      var checkbox = $('input[type=checkbox],.secured')
      checkbox.each(function(pos){
        checkbox[pos].checked = false;
      });
      alert(token.error);
    }
    else {
      var b = token[window.swaggerUi.tokenName];
      if(b){
        // if all roles are satisfied
        var o = null;
        $.each($('.auth .api-ic .api_information_panel'), function(k, v) {
          var children = v;
          if(children && children.childNodes) {
            var requiredScopes = [];
            $.each((children.childNodes), function (k1, v1){
              var inner = v1.innerHTML;
              if(inner)
                requiredScopes.push(inner);
            });
            var diff = [];
            for(var i=0; i < requiredScopes.length; i++) {
              var s = requiredScopes[i];
              if(window.enabledScopes && window.enabledScopes.indexOf(s) == -1) {
                diff.push(s);
              }
            }
            if(diff.length > 0){
              o = v.parentNode.parentNode;
              $(o.parentNode).find('.api-ic.ic-on').addClass('ic-off');
              $(o.parentNode).find('.api-ic.ic-on').removeClass('ic-on');

              // sorry, not all scopes are satisfied
              $(o).find('.api-ic').addClass('ic-warning');
              $(o).find('.api-ic').removeClass('ic-error');
            }
            else {
              o = v.parentNode.parentNode;
              $(o.parentNode).find('.api-ic.ic-off').addClass('ic-on');
              $(o.parentNode).find('.api-ic.ic-off').removeClass('ic-off');

              // all scopes are satisfied
              $(o).find('.api-ic').addClass('ic-info');
              $(o).find('.api-ic').removeClass('ic-warning');
              $(o).find('.api-ic').removeClass('ic-error');
            }
          }
        });
        window.swaggerUi.api.clientAuthorizations.add(oauth2KeyName, new SwaggerClient.ApiKeyAuthorization('Authorization', 'Bearer ' + b, 'header'));
      }
    }
  }
}