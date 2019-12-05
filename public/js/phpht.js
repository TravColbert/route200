/**
 * SUPPORT FUNCTIONS
 */
function filterErrors (data) {
  console.log('Checking for errors in response data')
  if (data.errors.length > 0) return new Error(data.errors)
  return data
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

/**
 * START THE JAVASCRIPT!
 */
ready(() => {
  stopPropagation()
})
