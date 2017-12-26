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
          <a href="#" title="Designing Mannequin 1.0">
            <img src={DesigningMannequin} alt="Designing Mannequin 1.0"/>
            <span className="BlogPosts__tag">Design</span>
            <span className="BlogPosts__title">Designing Mannequin 1.0</span>
          </a>
        </li>

        <li>
          <a href="#" title="Why we’re moving to component theming">
            <img src={ComponentTheming} alt="Why we’re moving to component theming"/>
            <span className="BlogPosts__tag">Coding + Development</span>
            <span className="BlogPosts__title">Why we’re moving to component theming</span>
          </a>
        </li>

        <li>
          <a href="#" title="Introducing Mannequin">
            <img src={IntroducingMannequin} alt="Introducing Mannequin"/>
            <span className="BlogPosts__tag">News</span>
            <span className="BlogPosts__title">Introducing Mannequin</span>
          </a>
        </li>

      </ul>

    </div>
  )
}
