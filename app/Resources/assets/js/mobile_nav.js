'use strict';

class Detabinator {
  constructor(element) {
    if (!element) {
      throw new Error('Missing required argument. new Detabinator needs an element reference');
    }
    this._inert = false;
    this._focusableElementsString = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex], [contenteditable]';
    this._focusableElements = Array.from(
      element.querySelectorAll(this._focusableElementsString)
    );
  }

  get inert() {
    return this._inert;
  }

  set inert(isInert) {
    if (this._inert === isInert) {
      return;
    }

    this._inert = isInert;

    this._focusableElements.forEach((child) => {
      if (isInert) {
        // If the child has an explict tabindex save it
        if (child.hasAttribute('tabindex')) {
          child.__savedTabindex = child.tabIndex;
        }
        // Set ALL focusable children to tabindex -1
        child.setAttribute('tabindex', -1);
      } else {
        // If the child has a saved tabindex, restore it
        // Because the value could be 0, explicitly check that it's not false
        if (child.__savedTabindex === 0 || child.__savedTabindex) {
          return child.setAttribute('tabindex', child.__savedTabindex);
        } else {
          // Remove tabindex from ANY REMAINING children
          child.removeAttribute('tabindex');
        }
      }
    });
  }
}

class SideNav {
  constructor () {
    this.showButtonEl = document.querySelector('.js-menu-show');
    this.hideButtonEl = document.querySelector('.js-menu-hide');
    this.sideNavEl = document.querySelector('.js-mobile-nav');
    this.sideNavContainerEl = document.querySelector('.js-mobile-nav-container');
    this.body = document.body;
    // Control whether the container's children can be focused
    // Set initial state to inert since the drawer is offscreen
    this.detabinator = new Detabinator(this.sideNavContainerEl);
    this.detabinator.inert = true;

    this.showSideNav = this.showSideNav.bind(this);
    this.hideSideNav = this.hideSideNav.bind(this);
    this.blockClicks = this.blockClicks.bind(this);
    this.onTouchStart = this.onTouchStart.bind(this);
    this.onTouchMove = this.onTouchMove.bind(this);
    this.onTouchEnd = this.onTouchEnd.bind(this);
    this.onTransitionEnd = this.onTransitionEnd.bind(this);
    this.update = this.update.bind(this);

    this.startX = 0;
    this.currentX = 0;
    this.touchingSideNav = false;

    this.transitionEndProperty = null;
    this.transitionEndTime = 0;

    this.supportsPassive = undefined;
    this.addEventListeners();
  }

  // apply passive event listening if it's supported
  applyPassive () {
    if (this.supportsPassive !== undefined) {
      return this.supportsPassive ? {passive: true} : false;
    }
    // feature detect
    let isSupported = false;
    try {
      document.addEventListener('test', null, {get passive () {
        isSupported = true;
      }});
    } catch (e) { }
    this.supportsPassive = isSupported;
    return this.applyPassive();
  }

  addEventListeners () {
    this.showButtonEl.addEventListener('click', this.showSideNav);
    this.hideButtonEl.addEventListener('click', this.hideSideNav);
    this.sideNavEl.addEventListener('click', this.hideSideNav);
    this.sideNavContainerEl.addEventListener('click', this.blockClicks);

    this.sideNavEl.addEventListener('touchstart', this.onTouchStart, this.applyPassive());
    this.sideNavEl.addEventListener('touchmove', this.onTouchMove, this.applyPassive());
    this.sideNavEl.addEventListener('touchend', this.onTouchEnd);
  }

  onTouchStart (evt) {
    if (!this.sideNavEl.classList.contains('mobile-nav--visible'))
      return;

    this.startX = evt.touches[0].pageX;
    this.currentX = this.startX;

    this.touchingSideNav = true;
    requestAnimationFrame(this.update);
  }

  onTouchMove (evt) {
    if (!this.touchingSideNav)
      return;

    this.currentX = evt.touches[0].pageX;
  }

  onTouchEnd (evt) {
    if (!this.touchingSideNav)
      return;

    this.touchingSideNav = false;

    const translateX = Math.min(0, this.currentX - this.startX);
    this.sideNavContainerEl.style.transform = '';

    if (translateX < 0) {
      this.hideSideNav();
    }
  }

  update () {
    if (!this.touchingSideNav)
      return;

    requestAnimationFrame(this.update);

    const translateX = Math.min(0, this.currentX - this.startX);
    this.sideNavContainerEl.style.transform = `translateX(${translateX}px)`;
  }

  blockClicks (evt) {
    evt.stopPropagation();
  }

  onTransitionEnd (evt) {
    if (evt.propertyName != this.transitionEndProperty && evt.elapsedTime!= this.transitionEndTime){
      return;
    }

    this.transitionEndProperty = null;
    this.transitionEndTime = 0;

    this.sideNavEl.classList.remove('mobile-nav--animatable');
    this.sideNavEl.removeEventListener('transitionend', this.onTransitionEnd);
  }

  showSideNav () {
    this.body.classList.add('noscroll');
    this.sideNavEl.classList.add('mobile-nav--animatable');
    this.sideNavEl.classList.add('mobile-nav--visible');
    this.detabinator.inert = false;

    this.transitionEndProperty = 'transform';
    // the duration of transition (make unique to distinguish transitions )
    this.transitionEndTime = 0.33;

    this.sideNavEl.addEventListener('transitionend', this.onTransitionEnd);
  }

  hideSideNav () {
    this.body.classList.remove('noscroll');
    this.sideNavEl.classList.add('mobile-nav--animatable');
    this.sideNavEl.classList.remove('mobile-nav--visible');
    this.detabinator.inert = true;

    this.transitionEndProperty = 'transform';
    this.transitionEndTime = 0.13;

    this.sideNavEl.addEventListener('transitionend', this.onTransitionEnd);
  }
}

new SideNav();


