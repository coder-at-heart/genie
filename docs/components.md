---
layout: page 
title: Components
nav_order: 3
---

# Components
{: .no_toc }
<details open markdown="block">
  <summary>
    Table of contents
  </summary>
  {: .text-delta }
1. TOC
{:toc}
</details>

##  Logical grouping
Genie expects you to divide up your code into self-contained classes 
called components.

Each class should implement the `GenieComponent` interface

Each component should implement a public static method called `setup` where 
Custom Posts are registered, hooks and filters defined etc
