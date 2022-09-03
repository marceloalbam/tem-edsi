(($) ->
  'use strict'
  $ ->
    pxPerREM = 16
    # Make sticky subsite nav menu offset by sticky site header height
    $firstTopSticky = $ '.sticky'
      .filter ( index )->
        return $(this).data('zfPlugin').options.stickTo is 'top' and
        Foundation.MediaQuery.atLeast($(this).data('zfPlugin').options.stickyOn) is true
      .first()
    $subsiteMenu = $('.subsite-menu .sticky-menu')

    # Account for changing site header height in subsite menu sticky positioning.
    updateSubsiteMenu = ()->
      $subsiteMenu.data('zfPlugin').options.marginTop = $firstTopSticky.outerHeight() / pxPerREM

    if $firstTopSticky.length > 0
      updateSubsiteMenu()
      $firstTopSticky
        .on 'sticky.zf.stuckto:top sticky.zf.unstuckfrom:top', updateSubsiteMenu


    # Top Bottom Sticky Feature
    $tbSticky = $ '.af4-sticky-top-bottom'
    if $tbSticky.length > 0
      # Convert data attribute to object
      $args = {}
      if $tbSticky.data 'ag-options'
        $args = '"' + $tbSticky.data('ag-options').replace(/;$/,'').replace(/:/g,'":"').replace(/;/g,'";"') + '"'
        $args = $args.replace(/:"([\d]+)"?/g,':$1').replace(/;/g,',')
        $args = '{' + $args + '}'
      $tbSticky.data 'ag-options', JSON.parse $args
      # Set CSS
      $tbSticky.css 'width', '100%'
      # Find top constraint target as existing top sticky
      $topOffsetTarget = $ '.sticky'
        .filter ( index )->
          return $(this).data('zfPlugin').options.stickTo is 'top' and
          Foundation.MediaQuery.atLeast($(this).data('zfPlugin').options.stickyOn) is true
      # Find element to anchor around
      $anchor = $ '.af4-sticky-top-bottom-anchor, .site-header .unit-header-wrap'

      positionMenu = (evt)->
        scrollPos = $(window).scrollTop()
        anchorOffset = $anchor.offset()
        otherStickyHeight = 0
        stickyHeightOffset = $tbSticky.outerHeight()
        if $tbSticky.data('ag-options') and $tbSticky.data('ag-options')["anchorMove"] and $tbSticky.data('ag-options')['anchorMove'] is 'inPlace'
          stickyHeightOffset = 0
          $tbSticky.parent().css 'height', $tbSticky.outerHeight()

        if $topOffsetTarget.length > 0
          otherStickyHeight = $topOffsetTarget.outerHeight()
        anchorDistanceFromViewport =
          top: anchorOffset.top - scrollPos,
          bottom: (scrollPos + window.innerHeight) - (anchorOffset.top + $anchor.outerHeight())
        if anchorDistanceFromViewport.bottom <= 0
          # If the bottom of the anchor is below or at the viewport, anchor the menu to the bottom.
          if $tbSticky.hasClass('anchorMove') is true
            $tbSticky.css('top', '').removeClass 'anchorMove'
          if $tbSticky.hasClass('anchorBottom') is false
            $tbSticky.removeClass('anchorTop').addClass 'anchorBottom'
        else if anchorDistanceFromViewport.bottom + stickyHeightOffset + otherStickyHeight >= window.innerHeight
          # If the bottom of the anchor plus the height of the menu is greater than or equal to the height of the viewport, anchor menu to top.
          if $tbSticky.hasClass('anchorMove') is true
            $tbSticky.css('top', '').removeClass 'anchorMove'
          if $tbSticky.hasClass('anchorTop') is false
            $tbSticky.removeClass('anchorBottom').addClass 'anchorTop'
              .css 'top', otherStickyHeight
        else if $tbSticky.hasClass('anchorMove') is false
          # Else scroll menu with container between top and bottom.
          if $tbSticky.hasClass('anchorTop') is true
            $tbSticky.removeClass 'anchorTop'
          if $tbSticky.hasClass('anchorBottom') is true
            $tbSticky.removeClass 'anchorBottom'
          $tbSticky.addClass 'anchorMove'
          bottomOfAnchor = anchorOffset.top + $anchor.outerHeight()
          topPositionOfMenu = bottomOfAnchor - $tbSticky.parent().offset().top - stickyHeightOffset
          topPositionOfMenu = parseInt topPositionOfMenu
          $tbSticky.css 'top', topPositionOfMenu

        if evt and evt.type is 'resize'
          if $tbSticky.hasClass('anchorTop') and parseInt($tbSticky.css('top')) isnt otherStickyHeight
            $tbSticky.css('top', otherStickyHeight)
          else if $tbSticky.hasClass('anchorBottom')
            $tbSticky.css('top', '')

      $(window).one 'load', ()->
        positionMenu()
        $(window).on 'scroll', positionMenu

      $(window).on 'resize', positionMenu

    return
) jQuery
