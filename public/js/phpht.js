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