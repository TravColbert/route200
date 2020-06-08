/**
 * SUPPORT FUNCTIONS
 */
function filterErrors (data) {
  console.log('Checking for errors in response data')
  if (data.errors.length > 0) return new Error(data.errors)
  return data
}

/**
 * GETTING FUNCTIONS
 * 
 * 'getting' is calling the appropriate 'fetch' function, processing 
 * the JSON results a little (filtering out errors), then 'setting' the 
 * results with the 'set?' functions.
 */
function getReport(reportName) {
  console.log(`Getting report: '${reportName}'`)
  return fetchReport(reportName)
  .then(response => {
    if(!response) throw new Error("unable to retrieve report")
    console.log(response)
    return filterErrors(response)
  })
  .then(response => {
    return animalList.setResult(response)
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
function fetchReport(report, queryString) {
  if(!report) return false;
  let qS = (queryString) ? queryString : ``;
  let url = `/${report}/json/${qS}`;
  console.log(`Fetching report: ${url}`);
  return fetch(url)
  .then(response => {
    return response.json();
  })
}

/**
 * Root component
 */
if(document.getElementById('animals')) {
  var animalList = new Vue({
    el:'#animals',
    data:{
      animals:[]
    },
    methods: {
      setResult : function(report) {
        return !!(this.$data.animals = report.result)
      }
    },
    template:`
    <div id="animals">
      <div v-for="(animal,index) in animals" v-bind:id="'animal_' + index">
        <details>
          <summary>{{animal.animal}}</summary> 
          <div class="description">{{animal.description}}</div>
        </details> 
      </div>
    </div>
    `
  })
}

// Register service worker.
if('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js')
    .then((reg) => {
      console.log('Service worker registered.', reg)
    }, function(err) {
      console.log('Service worker registration failed: ', err)
    })
  })
} else {
  console.log('Service worker not supported')
}

var fetchThing = function(thing) {
  getReport(thing);
} 

/**
 * START THE JAVASCRIPT!
 */
ready(() => {
  fetchThing("demo")
  //getReport('demo')
})
