host = "localhost.localhost"
app = "fever"
api = "api/v1"

/**
 * SUPPORT FUNCTIONS
 */
function filterErrors (data) {
  if(data.errors && data.errors.length > 0) {
    data.errors.forEach(err => {
      console.log(`Err: ${err}`)
    })
  }
  return data
}

function goTo(location) {
  return (document.location = location)
}

function hideElement(element) {
  $(element).addClass("hidden")
}

/**
 * Submits formdata from form
 * @param {} formId 
 */
function submitForm(formId,method,successView) {
  method = method || 'POST'
  successView = successView || '/'
  let form = document.forms[formId]
  let formData = new FormData(form)
  return fetch(form.action, {
    method: method,
    body: formData
  })
  .then(response => {
    if(!response) throw new Error("unable to retrieve form response")
    return response.json()
  })
  .then(response => {
    console.log(response)
    return filterErrors(response)
  })
  .then(response => {
    console.log(response)
    console.log("response_code:" + response.response_code)
    console.log("response_resource:" + response.resource)
    console.log("going to success view:" + successView)
    return goTo(successView)
  })
  .catch(err => {
    console.log(err.message)
  })
}

/**
 * GETTING FUNCTIONS
 * 
 * 'getting' is calling the appropriate 'fetch' function, processing 
 * the JSON results a little (filtering out errors), then 'setting' the 
 * results with the 'set?' functions.
 */
function get(url) {
  console.log(`GETting URL: '${url}'`)
  return fetchJSON(url)
  .then(response => {
    if(!response) throw new Error("unable to retrieve report")
    return filterErrors(response)
  })
  .then(response => {
    if(response.type) console.log(response.type)
    return response
  })
  .catch(err => {
    console.log("ERR: " + err.message)
  })
}

function getJSON(url) {
  console.log(`Getting JSON from: '${url}'`)
  return fetchJSON(url)
  .then(response => {
    if(!response) throw new Error("unable to retrieve report")
    return filterErrors(response)
  })
  .then(response => {
    // console.log(response.type)
    return app[response.type].setResult(response)
  })
  .catch(err => {
    console.log(err.message)
  })
}

/**
 * FETCHING FUNCTIONS
 * 
 * 'fetching' is just calling out to the server and getting the data.
 * There is no processing except transforming the data to JSON.
 */
function fetchJSON(report, queryString) {
  if(!report) return false
  let qS = (queryString) ? queryString : ``
  let url = `/${app}/${api}/${report}/${qS}`
  // console.log(`Fetching JSON: ${url}`)
  return fetch(url)
  .then(response => {
    return response.json()
  })
}

/**
 * PUTting FUNCTIONS
 */
function put(url, data, returnUrl) {
  console.log(`PUTting JSON from: '${url}'`)
  console.log(`Returning to: ${returnUrl}`)
  return fetch(url, {
    method: 'PUT',
    body: JSON.stringify(data),
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(response => {
    if(!response) throw new Error("unable to retrieve form response")
    return response.json()
  })
  .then(response => {
    return filterErrors(response)
  })
  .then(response => {
    console.log(response)
    console.log("response_code:" + response.response_code)
    console.log("response_resource:" + response.resource)
    return goTo(returnUrl)
  })
  .catch(err => {
    console.log(err.message)
  })
}

function post(url, data, returnUrl) {
  console.log(`POSTing to: '${url}'`)
  console.log(`Returning to: '${returnUrl}'`)
  console.log(JSON.stringify(data))
  return fetch(url, {
    method: 'POST',
    body: data
  })
  .then(response => {
    if(!response) throw new Error("unable to retrieve form response")
    return response.json()
  })
  .then(response => {
    return filterErrors(response)
  })
  .then(response => {
    console.log(response)
    console.log("response_code:" + response.response_code)
    console.log("response_resource:" + response.resource)
    return goTo(returnUrl)
  })
  .catch(err => {
    console.log(err.message)
  })
}

function stopClick (e) {
  e.stopPropagation()
}

function stopPropagation () {
  let inputs = document.querySelectorAll('.formelement.input')
  for (let input of inputs) {
    input.addEventListener('click', stopClick, false)
  }
}

Vue.component('domain-create-link', {
  props:[
    "href"
  ],
  template:`
    <div>
      <a :href="href" class="btn col-12">Create Domain</a>
    </div>
  `
})

Vue.component('domain-item', {
  props:[
    'domain'
  ],
  template:`
    <div :id="'domain_' + domain.id" class="tile tile-centered domain-item">
      <div class="tile-icon">
        <div class="example-tile-icon">
          <i class="icon icon-flag centered"></i>
        </div>
      </div>
      <div class="tile-content">
        <div class="tile-title">{{domain.name}}</div>
        <small class="tile-subtitle text-gray">{{domain.description}}</small>
      </div>
      <div class="tile-action">
        <button class="btn btn-link">
          <i class="icon icon-more-vert"></i>
        </button>
      </div>
    </div>
  `
})

Vue.component('user-create-link', {
  props:[
    "href"
  ],
  template:`
    <div>
      <a :href="href" class="btn col-12"><i class="icon icon-plus"></i> Create User</a>
    </div>
  `
})

Vue.component('user-item', {
  props:[
    'user'
  ],
  template:`
    <div :id="'user_' + user.id" class="tile tile-centered user-item">
      <div class="tile-icon">
        <div class="example-tile-icon">
          <i class="icon icon-people centered"></i>
        </div>
      </div>
      <div class="tile-content">
        <div class="tile-title">{{user.email}}</div>
        <small class="tile-subtitle text-gray">{{user.username}}</small>
      </div>
      <div class="tile-action">
        <button class="btn btn-link">
          <i class="icon icon-more-vert"></i>
        </button>
      </div>
    </div>
  `
})

if(document.getElementById('admin-domains')) {
  var adminDomains = new Vue({
    el:'#admin-domains',
    data:{
      domains: Array,
      createLinkDomains: false
    },
    computed:{
      domainsExist: function() {
        console.log(`Users found: ${this.$data.domains.length}`)
        return !!(this.$data.domains.length)
      }
    },
    beforeCreate: function() {
      return get('domains')
      .then(response => {
        this.$data.domains = response.result || false
        return get('domains/ui/create')
      })
      .then(response => {
        this.$data.createLinkDomains = response.result
      })
      .catch(err => {
        console.log(err.message)
      })
    },
    template: `
      <div id="admin-domains" v-show="domainsExist" class="accordion">
        <input type="checkbox" id="admin-domains-1" name="admin-accordion" hidden>
        <label class="accordion-header" for="admin-domains-1">
          <div class="h5"><i class="icon icon-arrow-right mr-1"></i><span class="badge" :data-badge="domains.length">Domains</span></div>
        </label>
        <div class="accordion-body">
          <domain-item v-for="(domain,index) in domains" :domain="domain" :key="index"></domain-item>
          <domain-create-link v-show="createLinkDomains" :href="createLinkDomains"></domain-create-link>
        </div>
      </div>
    `
  })
}

if(document.getElementById('admin-users')) {
  var adminUsers = new Vue({
    el:'#admin-users',
    data:{
      users: Array
      ,response_code: false
      ,createLinkUsers: false
    },
    computed:{
      usersExist: function() {
        console.log(`Users found: ${this.$data.users.length}`)
        return !!(this.$data.users.length)
      }
    },
    beforeCreate: function() {
      return get('users')
      .then(response => {
        if(response.result) {
          this.$data.users = response.result
        } else {
          this.$data.users = false
        }
        return get('users/ui/create')
      })
      .then(response => {
        console.log(`amdin-users: ${response.result}`)
        this.$data.response_code = response.response_code
        return (this.$data.createLinkUsers = response.result)
      })
      .catch(err => {
        console.log(err.message)
      })
    },
    template: `
      <div id="admin-users" v-show="usersExist" class="accordion">
        <input type="checkbox" id="admin-users-1" name="admin-accordion" hidden>
        <label class="accordion-header" for="admin-users-1">
          <div class="h5"><i class="icon icon-arrow-right mr-1"></i><span class="badge" :data-badge="users.length">Users</span></div>
        </label>
        <div class="accordion-body">
          <user-item v-for="(user,index) in users" :user="user" :key="index"></user-item>
          <user-create-link v-show="createLinkUsers" :href="createLinkUsers"></user-create-link>
        </div>
      </div>
    `
  })
}

if(document.getElementById('domains-create')) {
  var domainsCreate = new Vue({
    el:'#domains-create',
    data:{
      name: null,
      description: null
    },
    methods:{
      submitForm: function(formId,method,successView) {
        submitForm(formId,method,successView)
      }
    },
    template: `
      <article id="domains-create" class="tile">
        <div class="tile-icon">
          <figure class="avatar avatar-lg p-2">
            <i class="icon icon-edit icon-2x"></i>
          </figure>
        </div>
        <div class="tile-content">
          <div class="tile-title h4">
            <span>Domains Create</span>
          </div> 
          <div class="tile-subtitle">
            <span>Create domain</span>
          </div>
          <form id="domain_create" method="POST" v-bind:action="/domains/">
            <div class="form-group">
              <label for="name" class="form-label h5">Domain Name</label>
              <input type="text" name="name" id="name" v-model="name" class="col-12">
            </div>
            <div class="form-group">
              <label for="description" class="form-label h5">Description</label>
              <input type="text" name="description" id="description" v-model="description" class="col-12">
            </div>
            <div class="form-group">
              <input type="button" class="btn btn-primary" name="create" value="Create Domain" v-bind:disabled="(!name)" v-on:click="submitForm('domain_create','POST','/admin/')">
              <input type="button" class="btn" name="cancel" value="Cancel" onClick="goTo('/')">
            </div>
          </form>
        </div>
        <div class="tile-action">
          <div class="dropdown dropdown-right">
            <a href="#" class="btn btn-link dropdown-toggle" tabindex="0">
              <i class="icon icon-more-vert"></i>
            </a>
            <ul class="menu">
              <!--
              <li>Menu 1</li>
              <li>Menu 2</li>
              -->
            </ul>
          </div>
        </div>
      </article>
    `
  })
}

if(document.getElementById('domains-edit')) {
  var domainsEdit = new Vue({
    el:'#domains-edit',
    data:{

    },
    beforeCreate: function() {

    }
  })
}

if(document.getElementById('users-create')) {
  var usersCreate = new Vue({
    el:'#users-create',
    data:{
      email: null,
      username: null,
      password: null,
      passwordconfirm: null,
      domain: null,
      domains: Array,
      role: null,
      roles: Array
    },
    methods:{
      submitForm: function(formId,method,successView) {
        submitForm(formId,method,successView)
      }
    },
    beforeCreate: function() {
      return get('domains')
      .then(response => {
        this.$data.domains = response.result
        return get('roles')
      })
      .then(response => {
        this.$data.roles = response.result
      })
    },
    template: `
      <article id="users-create" class="tile">
        <div class="tile-icon">
          <figure class="avatar avatar-lg p-2">
            <i class="icon icon-edit icon-2x"></i>
          </figure>
        </div>
        <div class="tile-content">
          <div class="tile-title h4">
            <span>Create User</span>
          </div> 
          <div class="tile-subtitle">
            <span>Create user account</span>
          </div>
          <form id="user_create" method="POST" v-bind:action="/users/">
            <div class="form-group">
              <label for="email" class="form-label h5">Email Address</label>
              <input type="text" name="email" id="email" v-model="email" class="col-12 noLastPassStyle">
            </div>
            <div class="form-group">
              <label for="username" class="form-label h5">Username</label>
              <input type="text" name="username" id="username" v-model="username" class="col-12 noLastPassStyle">
            </div>
            <div class="form-group">
              <label for="password" class="form-label h5">Password</label>
              <input type="password" name="password" id="password" v-model="password" class="col-12 noLastPassStyle">
            </div>
            <div class="form-group">
              <label for="password-confirm" class="form-label h5">Password Confirm</label>
              <input type="password" name="password-confirm" id="password-confirm" v-model="passwordconfirm" class="col-12 noLastPassStyle">
            </div>
            <div class="form-group">
              <label for="domainid" class="form-label h5">Domain</label>
              <select name="domainid" id="domainid" v-model="domain" class="col-12">
                <option disabled value="">Please select domain</option>
                <option v-for="(domain,index) in domains" :value="domain.id" :key="index">{{domain.name}}</option>
              </select>
            </div>
            <div class="form-group">
              <label for="roleid" class="form-label h5">Role</label>
              <select name="roleid" id="roleid" v-model="role" class="col-12">
                <option disabled value="">Please select role</option>
                <option v-for="(role,index) in roles" :value="role.id" :key="index">{{role.name}}</option>
              </select>
            </div>
            <div class="form-group">
              <input type="button" class="btn btn-primary" value="Create User" v-bind:disabled="(password!==passwordconfirm)" v-on:click="submitForm('user_create','POST','/admin/')" name="create">
              <input type="button" class="btn" name="cancel" value="Cancel" onClick="goTo('/')">
            </div>
          </form>
        </div>
        <div class="tile-action">
          <div class="dropdown dropdown-right">
            <a href="#" class="btn btn-link dropdown-toggle" tabindex="0">
              <i class="icon icon-more-vert"></i>
            </a>
            <ul class="menu">
              <li>Check out</li>
              <li>Map</li>
            </ul>
          </div>
        </div>
      </article>
    `
  })
}

/**
 * PAGE FUNCTIONS
 */
function ready (fn) {
  if (document.readyState !== 'loading') {
    fn()
  } else {
    document.addEventListener('DOMContentLoaded', fn)
  }
}