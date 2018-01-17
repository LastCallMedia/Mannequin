import React from 'react'
import DesigningMannequin from '../img/blog-posts/designing-mannequin.png'
import ComponentTheming from '../img/blog-posts/component-theming.png'
import IntroducingMannequin from '../img/blog-posts/introducing-mannequin.jpg'
import './BlogPosts.scss'

export default function BlogPosts() {
  return (
    <div className="BlogPosts">

      <h2>Blog Posts</h2>

      <ul>

        <li>
          <a href="https://lastcallmedia.com/blog/Designing-Mannequin-1.0" title="Designing Mannequin 1.0">
            <img src={DesigningMannequin} alt="Designing Mannequin 1.0"/>
            <span className="tag">Design</span>
            <span className="title">Designing Mannequin 1.0</span>
          </a>
        </li>

        <li>
          <a href="https://lastcallmedia.com/blog/why-component-theming" title="Why we’re moving to component theming">
            <img src={ComponentTheming} alt="Why we’re moving to component theming"/>
            <span className="tag">Coding + Development</span>
            <span className="title">Why we’re moving to component theming</span>
          </a>
        </li>

        <li>
          <a href="https://lastcallmedia.com/blog/introducing-mannequin" title="Introducing Mannequin">
            <img src={IntroducingMannequin} alt="Introducing Mannequin"/>
            <span className="tag">News</span>
            <span className="title">Introducing Mannequin</span>
          </a>
        </li>

      </ul>

    </div>
  )
}
