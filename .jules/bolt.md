## 2024-04-05 - Optimize KanbanBoard filtering performance
**Learning:** The Kanban board component was calling `.filter()` twice per column on the full `orders` array during every render. In a Kanban board with 6 columns, this results in 12 iterations over the entire orders array per render. For a large number of orders, this creates a rendering bottleneck.
**Action:** Group orders by status using `useMemo` to build a dictionary of orders by status. This reduces the time complexity from O(N*C) to O(N) during renders, requiring only a single pass over the data when it changes.
