(($) ->
  'use strict'
  # Ensure the Exceptional Item sidebar is never too close to the site header.
  # Both elements are sticky and the header shrinks when it is stuck to the top.

  $header = $ '.site-header [data-sticky]'
  $content = $ '#genesis-content'
  $sticky = $ '#genesis-content .right [data-sticky]'

  transition = 15 + 1000 * parseFloat(
    $ '.site-header .layout-container'
    .css 'transition-duration'
    .split(', ')[0]
  )
  fontSize = parseFloat $sticky.css 'font-size'
  spaceBelowHeader = $content.offset().top - $header.height()

  $ window
    .one 'load.zf.sticky', ->
      calcStickyTop()

  calcStickyTop = ->
    topOffset = $header.height() + spaceBelowHeader
    ems = .1 * Math.ceil topOffset / fontSize * 10

    $sticky.attr 'data-margin-top', ems
    $sticky.data 'marginTop', ems
    if $sticky.hasClass 'is-stuck'
      $sticky.css 'margin-top', ems + 'em'
    if $sticky.data 'zfPlugin'
      $sticky.data('zfPlugin').options.marginTop = ems
    return

  $header.on 'sticky.zf.stuckto:top', ->
    window.setTimeout calcStickyTop, transition
    return
  $header.on 'sticky.zf.unstuckfrom:top', ->
    window.setTimeout calcStickyTop, transition
    return

  return
) jQuery
